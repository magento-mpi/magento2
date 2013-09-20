<?php
/**
 * Abstract block context object. Will be used as block constructor modification point after release.
 * Important: Should not be modified by extension developers.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Core_Block_Context implements Magento_ObjectManager_ContextInterface
{
    /**
     * @var Magento_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * @var Magento_Core_Model_Layout
     */
    protected $_layout;

    /**
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager;

    /**
     * @var Magento_Core_Model_UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var Magento_Core_Model_Translate
     */
    protected $_translator;

    /**
     * @var Magento_Core_Model_CacheInterface
     */
    protected $_cache;

    /**
     * @var Magento_Core_Model_View_DesignInterface
     */
    protected $_design;

    /**
     * @var Magento_Core_Model_Session
     */
    protected $_session;

    /**
     * @var Magento_Core_Model_Store_Config
     */
    protected $_storeConfig;

    /**
     * @var Magento_Core_Controller_Varien_Front
     */
    protected $_frontController;

    /**
     * @var Magento_Core_Model_Factory_Helper
     */
    protected $_helperFactory;

    /**
     * @var Magento_Core_Model_View_Url
     */
    protected $_viewUrl;

    /**
     * View config model
     *
     * @var Magento_Core_Model_View_Config
     */
    protected $_viewConfig;

    /**
     * @var Magento_Core_Model_Cache_StateInterface
     */
    protected $_cacheState;

    /**
     * @var Magento_Core_Model_Logger
     */
    protected $_logger;

    /**
     * @param Magento_Core_Controller_Request_Http $request
     * @param Magento_Core_Model_Layout $layout
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_UrlInterface $urlBuilder
     * @param Magento_Core_Model_Translate $translator
     * @param Magento_Core_Model_CacheInterface $cache
     * @param Magento_Core_Model_View_DesignInterface $design
     * @param Magento_Core_Model_Session_Abstract $session
     * @param Magento_Core_Model_Store_Config $storeConfig
     * @param Magento_Core_Controller_Varien_Front $frontController
     * @param Magento_Core_Model_Factory_Helper $helperFactory
     * @param Magento_Core_Model_View_Url $viewUrl
     * @param Magento_Core_Model_View_Config $viewConfig
     * @param Magento_Core_Model_Cache_StateInterface $cacheState
     * @param Magento_Core_Model_Logger $logger
     * @param array $data
     */
    public function __construct(
        Magento_Core_Controller_Request_Http $request,
        Magento_Core_Model_Layout $layout,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_UrlInterface $urlBuilder,
        Magento_Core_Model_Translate $translator,
        Magento_Core_Model_CacheInterface $cache,
        Magento_Core_Model_View_DesignInterface $design,
        Magento_Core_Model_Session_Abstract $session,
        Magento_Core_Model_Store_Config $storeConfig,
        Magento_Core_Controller_Varien_Front $frontController,
        Magento_Core_Model_Factory_Helper $helperFactory,
        Magento_Core_Model_View_Url $viewUrl,
        Magento_Core_Model_View_Config $viewConfig,
        Magento_Core_Model_Cache_StateInterface $cacheState,
        Magento_Core_Model_Logger $logger,
        array $data = array()
    ) {
        $this->_request         = $request;
        $this->_layout          = $layout;
        $this->_eventManager    = $eventManager;
        $this->_urlBuilder      = $urlBuilder;
        $this->_translator      = $translator;
        $this->_cache           = $cache;
        $this->_design          = $design;
        $this->_session         = $session;
        $this->_storeConfig     = $storeConfig;
        $this->_frontController = $frontController;
        $this->_helperFactory   = $helperFactory;
        $this->_viewUrl         = $viewUrl;
        $this->_viewConfig      = $viewConfig;
        $this->_cacheState      = $cacheState;
        $this->_logger          = $logger;
    }

    /**
     * @return Magento_Core_Model_CacheInterface
     */
    public function getCache()
    {
        return $this->_cache;
    }

    /**
     * @return Magento_Core_Model_View_DesignInterface
     */
    public function getDesignPackage()
    {
        return $this->_design;
    }

    /**
     * @return Magento_Core_Model_Event_Manager
     */
    public function getEventManager()
    {
        return $this->_eventManager;
    }

    /**
     * @return Magento_Core_Controller_Varien_Front
     */
    public function getFrontController()
    {
        return $this->_frontController;
    }

    /**
     * @return Magento_Core_Model_Factory_Helper
     */
    public function getHelperFactory()
    {
        return $this->_helperFactory;
    }

    /**
     * @return Magento_Core_Model_Layout
     */
    public function getLayout()
    {
        return $this->_layout;
    }

    /**
     * @return Magento_Core_Controller_Request_Http
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * @return Magento_Core_Model_Session|Magento_Core_Model_Session_Abstract
     */
    public function getSession()
    {
        return $this->_session;
    }

    /**
     * @return Magento_Core_Model_Store_Config
     */
    public function getStoreConfig()
    {
        return $this->_storeConfig;
    }

    /**
     * @return Magento_Core_Model_Translate
     */
    public function getTranslator()
    {
        return $this->_translator;
    }

    /**
     * @return Magento_Core_Model_UrlInterface
     */
    public function getUrlBuilder()
    {
        return $this->_urlBuilder;
    }

    /**
     * @return Magento_Core_Model_View_Url
     */
    public function getViewUrl()
    {
        return $this->_viewUrl;
    }

    /**
     * @return Magento_Core_Model_View_Config
     */
    public function getViewConfig()
    {
        return $this->_viewConfig;
    }

    /**
     * @return \Magento_Core_Model_Cache_StateInterface
     */
    public function getCacheState()
    {
        return $this->_cacheState;
    }

    /**
     * @return \Magento_Core_Model_Logger
     */
    public function getLogger()
    {
        return $this->_logger;
    }
}
