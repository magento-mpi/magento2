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

class Area implements \Magento\Framework\App\AreaInterface
{
    const AREA_GLOBAL = 'global';

    const AREA_FRONTEND = 'frontend';
    
    const AREA_ADMIN    = 'admin';

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
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * Translator
     *
     * @var \Magento\Framework\TranslateInterface
     */
    protected $_translator;

    /**
     * Application config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_config;

    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\App\ObjectManager\ConfigLoader
     */
    protected $_diConfigLoader;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Logger
     */
    protected $_logger;

    /**
     * Core design
     *
     * @var \Magento\Core\Model\Design
     */
    protected $_design;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Area\DesignExceptions
     */
    protected $_designExceptions;

    /**
     * @param \Magento\Framework\Logger $logger
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\TranslateInterface $translator
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param \Magento\Framework\App\ObjectManager\ConfigLoader $diConfigLoader
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Core\Model\Design $design
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Area\DesignExceptions $designExceptions
     * @param string $areaCode
     */
    public function __construct(
        \Magento\Framework\Logger $logger,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\TranslateInterface $translator,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\ObjectManager $objectManager,
        \Magento\Framework\App\ObjectManager\ConfigLoader $diConfigLoader,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Core\Model\Design $design,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Area\DesignExceptions $designExceptions,
        $areaCode
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_code = $areaCode;
        $this->_config = $config;
        $this->_objectManager = $objectManager;
        $this->_diConfigLoader = $diConfigLoader;
        $this->_eventManager = $eventManager;
        $this->_translator = $translator;
        $this->_logger = $logger;
        $this->_design = $design;
        $this->_storeManager = $storeManager;
        $this->_designExceptions = $designExceptions;
    }

    /**
     * Load area data
     *
     * @param   string|null $part
     * @return  $this
     */
    public function load($part = null)
    {
        if (is_null($part)) {
            $this->_loadPart(self::PART_CONFIG)->_loadPart(self::PART_DESIGN)->_loadPart(self::PART_TRANSLATE);
        } else {
            $this->_loadPart($part);
        }
        return $this;
    }

    /**
     * Detect and apply design for the area
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return void
     */
    public function detectDesign($request = null)
    {
        if ($this->_code == self::AREA_FRONTEND) {
            $isDesignException = $request && $this->_applyUserAgentDesignException($request);
            if (!$isDesignException) {
                $this->_design->loadChange(
                    $this->_storeManager->getStore()->getId()
                )->changeDesign(
                    $this->_getDesign()
                );
            }
        }
    }

    /**
     * Analyze user-agent information to override custom design settings
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    protected function _applyUserAgentDesignException($request)
    {
        try {
            $theme = $this->_designExceptions->getThemeForUserAgent($request);
            if (false !== $theme) {
                $this->_getDesign()->setDesignTheme($theme);
                return true;
            }
        } catch (\Exception $e) {
            $this->_logger->logException($e);
        }
        return false;
    }

    /**
     * @return \Magento\Framework\View\DesignInterface
     */
    protected function _getDesign()
    {
        return $this->_objectManager->get('Magento\Framework\View\DesignInterface');
    }

    /**
     * Loading part of area
     *
     * @param   string $part
     * @return  $this
     */
    protected function _loadPart($part)
    {
        if (isset($this->_loadedParts[$part])) {
            return $this;
        }
        \Magento\Framework\Profiler::start(
            'load_area:' . $this->_code . '.' . $part,
            array('group' => 'load_area', 'area_code' => $this->_code, 'part' => $part)
        );
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
        \Magento\Framework\Profiler::stop('load_area:' . $this->_code . '.' . $part);
        return $this;
    }

    /**
     * Load area configuration
     *
     * @return void
     */
    protected function _initConfig()
    {
        $this->_objectManager->configure($this->_diConfigLoader->load($this->_code));
    }

    /**
     * Initialize translate object.
     *
     * @return $this
     */
    protected function _initTranslate()
    {
        $this->_translator->loadData(null, false);

        \Magento\Framework\Phrase::setRenderer($this->_objectManager->get('Magento\Framework\Phrase\RendererInterface'));

        return $this;
    }

    /**
     * @return void
     */
    protected function _initDesign()
    {
        $this->_getDesign()->setArea($this->_code)->setDefaultDesignTheme();
    }
}
