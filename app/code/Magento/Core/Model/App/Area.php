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
class Magento_Core_Model_App_Area
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
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager;

    /**
     * Translator
     *
     * @var Magento_Core_Model_Translate
     */
    protected $_translator;

    /**
     * Application config
     *
     * @var Magento_Core_Model_Config
     */
    protected $_config;

    /**
     * Object manager
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Core_Model_ObjectManager_ConfigLoader
     */
    protected $_diConfigLoader;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Translate $translator
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_ObjectManager $objectManager
     * @param Magento_Core_Model_ObjectManager_ConfigLoader $diConfigLoader
     * @param string $areaCode
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Translate $translator,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_ObjectManager $objectManager,
        Magento_Core_Model_ObjectManager_ConfigLoader $diConfigLoader,
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
     * @return  Magento_Core_Model_App_Area
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
     * @param Zend_Controller_Request_Http $request
     */
    public function detectDesign($request = null)
    {
        if ($this->_code == self::AREA_FRONTEND) {
            $isDesignException = ($request && $this->_applyUserAgentDesignException($request));
            if (!$isDesignException) {
                $this->_getDesignChange()
                    ->loadChange(Mage::app()->getStore()->getId())
                    ->changeDesign($this->_getDesign());
            }
        }
    }

    /**
     * Analyze user-agent information to override custom design settings
     *
     * @param Zend_Controller_Request_Http $request
     * @return bool
     */
    protected function _applyUserAgentDesignException($request)
    {
        $userAgent = $request->getServer('HTTP_USER_AGENT');
        if (empty($userAgent)) {
            return false;
        }
        try {
            $expressions = Mage::getStoreConfig('design/theme/ua_regexp');
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
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return false;
    }

    /**
     * @return Magento_Core_Model_View_DesignInterface
     */
    protected function _getDesign()
    {
        return Mage::getDesign();
    }

    /**
     * @return Magento_Core_Model_Design
     */
    protected function _getDesignChange()
    {
        return Mage::getSingleton('Magento_Core_Model_Design');
    }

    /**
     * Loading part of area
     *
     * @param   string $part
     * @return  Magento_Core_Model_App_Area
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
     * @return Magento_Core_Model_App_Area
     */
    protected function _initTranslate()
    {
        $dispatchResult = new \Magento\Object(array(
            'inline_type' => null,
            'params' => array('area' => $this->_code)
        ));
        $eventManager = $this->_objectManager->get('Magento_Core_Model_Event_Manager');
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
