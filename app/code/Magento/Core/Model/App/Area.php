<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Application area model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model\App;

class Area
{
    const AREA_GLOBAL   = 'global';
    const AREA_FRONTEND = 'frontend';
    const AREA_ADMIN    = 'admin';
    const AREA_ADMINHTML = 'adminhtml';

    const PART_CONFIG   = 'config';
    const PART_TRANSLATE= 'translate';
    const PART_DESIGN   = 'design';

    /**
     * Area parameter.
     */
    const PARAM_AREA = 'area';

    /**
     * Array of area loaded parts
     *
     * @var array
     */
    protected $_loadedParts;

    /**
     * Area code
     *
     * @var string
     */
    protected $_code;

    /**
     * Event Manager
     *
     * @var \Magento\Core\Model\Event\Manager
     */
    protected $_eventManager;

    /**
     * Translator
     *
     * @var \Magento\Core\Model\Translate
     */
    protected $_translator;

    /**
     * Application config
     *
     * @var \Magento\Core\Model\Config
     */
    protected $_config;

    /**
     * Object manager
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Core\Model\ObjectManager\ConfigLoader
     */
    protected $_diConfigLoader;

    /**
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Model\Translate $translator
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\Core\Model\ObjectManager $objectManager
     * @param \Magento\Core\Model\ObjectManager\ConfigLoader $diConfigLoader
     * @param string $areaCode
     */
    public function __construct(
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Model\Translate $translator,
        \Magento\Core\Model\Config $config,
        \Magento\Core\Model\ObjectManager $objectManager,
        \Magento\Core\Model\ObjectManager\ConfigLoader $diConfigLoader,
        $areaCode
    ) {
        $this->_code = $areaCode;
        $this->_config = $config;
        $this->_objectManager = $objectManager;
        $this->_diConfigLoader = $diConfigLoader;
        $this->_eventManager = $eventManager;
        $this->_translator = $translator;
    }

    /**
     * Load area data
     *
     * @param   string|null $part
     * @return  \Magento\Core\Model\App\Area
     */
    public function load($part=null)
    {
        if (is_null($part)) {
            $this->_loadPart(self::PART_CONFIG)
                ->_loadPart(self::PART_DESIGN)
                ->_loadPart(self::PART_TRANSLATE);
        } else {
            $this->_loadPart($part);
        }
        return $this;
    }

    /**
     * Detect and apply design for the area
     *
     * @param \Zend_Controller_Request_Http $request
     */
    public function detectDesign($request = null)
    {
        if ($this->_code == self::AREA_FRONTEND) {
            $isDesignException = ($request && $this->_applyUserAgentDesignException($request));
            if (!$isDesignException) {
                $this->_getDesignChange()
                    ->loadChange(\Mage::app()->getStore()->getId())
                    ->changeDesign($this->_getDesign());
            }
        }
    }

    /**
     * Analyze user-agent information to override custom design settings
     *
     * @param \Zend_Controller_Request_Http $request
     * @return bool
     */
    protected function _applyUserAgentDesignException($request)
    {
        $userAgent = $request->getServer('HTTP_USER_AGENT');
        if (empty($userAgent)) {
            return false;
        }
        try {
            $expressions = \Mage::getStoreConfig('design/theme/ua_regexp');
            if (!$expressions) {
                return false;
            }
            $expressions = unserialize($expressions);
            foreach ($expressions as $rule) {
                if (preg_match($rule['regexp'], $userAgent)) {
                    $this->_getDesign()->setDesignTheme($rule['value']);
                    return true;
                }
            }
        } catch (\Exception $e) {
            \Mage::logException($e);
        }
        return false;
    }

    /**
     * @return \Magento\Core\Model\View\DesignInterface
     */
    protected function _getDesign()
    {
        return $this->_objectManager->get('Magento\Core\Model\View\DesignInterface');
    }

    /**
     * @return \Magento\Core\Model\Design
     */
    protected function _getDesignChange()
    {
        return \Mage::getSingleton('Magento\Core\Model\Design');
    }

    /**
     * Loading part of area
     *
     * @param   string $part
     * @return  \Magento\Core\Model\App\Area
     */
    protected function _loadPart($part)
    {
        if (isset($this->_loadedParts[$part])) {
            return $this;
        }
        \Magento\Profiler::start('load_area:' . $this->_code . '.' . $part,
            array('group' => 'load_area', 'area_code' => $this->_code, 'part' => $part));
        switch ($part) {
            case self::PART_CONFIG:
                $this->_initConfig();
                break;
            case self::PART_TRANSLATE:
                $this->_initTranslate();
                break;
            case self::PART_DESIGN:
                $this->_initDesign();
                break;
        }
        $this->_loadedParts[$part] = true;
        \Magento\Profiler::stop('load_area:' . $this->_code . '.' . $part);
        return $this;
    }

    /**
     * Load area configuration
     */
    protected function _initConfig()
    {
        $this->_objectManager->configure($this->_diConfigLoader->load($this->_code));
    }

    /**
     * Initialize translate object.
     *
     * @return \Magento\Core\Model\App\Area
     */
    protected function _initTranslate()
    {
        $dispatchResult = new \Magento\Object(array(
            'inline_type' => null,
            'params' => array('area' => $this->_code)
        ));
        $eventManager = $this->_objectManager->get('Magento\Core\Model\Event\Manager');
        $eventManager->dispatch('translate_initialization_before', array(
            'translate_object' => $this->_translator,
            'result' => $dispatchResult
        ));
        $this->_translator->init($this->_code, $dispatchResult, false);

        \Magento\Phrase::setRenderer($this->_objectManager->get('Magento\Phrase\RendererInterface'));
        return $this;
    }

    protected function _initDesign()
    {
        $this->_getDesign()->setArea($this->_code)->setDefaultDesignTheme();
    }
}
