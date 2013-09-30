<?php
/**
 * Core Session Context Model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Session_Context implements Magento_ObjectManager_ContextInterface
{
    /**
     * @var Magento_Core_Model_Session_Validator
     */
    protected $_validator;

    /**
     * @var Magento_Core_Model_Logger
     */
    protected $_logger;

    /**
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager;

    /**
     * @var Magento_Core_Helper_Http
     */
    protected $_httpHelper;

    /**
     * @var Magento_Core_Model_Store_Config
     */
    protected $_storeConfig;

    /**
     * @var string
     */
    protected $_saveMethod;

    /**
     * @var string
     */
    protected $_savePath;

    /**
     * @var string
     */
    protected $_cacheLimiter;

    /**
     * Mapping between area and SID param name
     *
     * @var array
     */
    protected $sidMap;

    /**
     * Core cookie
     *
     * @var Magento_Core_Model_Cookie
     */
    protected $_cookie;

    /**
     * Core message
     *
     * @var Magento_Core_Model_Message
     */
    protected $_message;

    /**
     * Core message collection factory
     *
     * @var Magento_Core_Model_Message_CollectionFactory
     */
    protected $_messageFactory;

    /**
     * @var Magento_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * @var Magento_Core_Model_App_State
     */
    protected $_appState;

    /**
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @var Magento_Core_Model_Dir
     */
    protected $_dir;

    /**
     * @var Magento_Core_Model_Url
     */
    protected $_url;

    /**
     * @param Magento_Core_Model_Session_Validator $validator
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Helper_Http $coreHttp
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_Message_CollectionFactory $messageFactory
     * @param Magento_Core_Model_Message $message
     * @param Magento_Core_Model_Cookie $cookie
     * @param Magento_Core_Controller_Request_Http $request
     * @param Magento_Core_Model_App_State $appState
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_Dir $dir
     * @param Magento_Core_Model_Url $url
     * @param $saveMethod
     * @param null $savePath
     * @param null $cacheLimiter
     * @param array $sidMap
     */
    public function __construct(
        Magento_Core_Model_Session_Validator $validator,
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Helper_Http $coreHttp,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_Message_CollectionFactory $messageFactory,
        Magento_Core_Model_Message $message,
        Magento_Core_Model_Cookie $cookie,
        Magento_Core_Controller_Request_Http $request,
        Magento_Core_Model_App_State $appState,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Dir $dir,
        Magento_Core_Model_Url $url,
        $saveMethod,
        $savePath = null,
        $cacheLimiter = null,
        $sidMap = array()
    ) {
        $this->_validator = $validator;
        $this->_logger = $logger;
        $this->_eventManager = $eventManager;
        $this->_httpHelper = $coreHttp;
        $this->_storeConfig = $coreStoreConfig;
        $this->_saveMethod = $saveMethod;
        $this->_savePath = $savePath;
        $this->_cacheLimiter = $cacheLimiter;
        $this->sidMap = $sidMap;
        $this->_messageFactory = $messageFactory;
        $this->_message = $message;
        $this->_cookie = $cookie;
        $this->_request = $request;
        $this->_appState = $appState;
        $this->_storeManager = $storeManager;
        $this->_dir = $dir;
        $this->_url = $url;
    }

    /**
     * @return \Magento_Core_Model_Event_Manager
     */
    public function getEventManager()
    {
        return $this->_eventManager;
    }

    /**
     * @return \Magento_Core_Helper_Http
     */
    public function getHttpHelper()
    {
        return $this->_httpHelper;
    }

    /**
     * @return \Magento_Core_Model_Logger
     */
    public function getLogger()
    {
        return $this->_logger;
    }

    /**
     * @return \Magento_Core_Model_Store_Config
     */
    public function getStoreConfig()
    {
        return $this->_storeConfig;
    }

    /**
     * @return \Magento_Core_Model_Session_Validator
     */
    public function getValidator()
    {
        return $this->_validator;
    }

    /**
     * @return string
     */
    public function getCacheLimiter()
    {
        return $this->_cacheLimiter;
    }

    /**
     * @return string
     */
    public function getSaveMethod()
    {
        return $this->_saveMethod;
    }

    /**
     * @return string
     */
    public function getSavePath()
    {
        return $this->_savePath;
    }

    /**
     * @return array
     */
    public function getSidMap()
    {
        return $this->sidMap;
    }

    /**
     * @return \Magento_Core_Model_App_State
     */
    public function getAppState()
    {
        return $this->_appState;
    }

    /**
     * @return \Magento_Core_Model_Cookie
     */
    public function getCookie()
    {
        return $this->_cookie;
    }

    /**
     * @return \Magento_Core_Model_Dir
     */
    public function getDir()
    {
        return $this->_dir;
    }

    /**
     * @return \Magento_Core_Model_Message
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     * @return \Magento_Core_Model_Message_CollectionFactory
     */
    public function getMessageFactory()
    {
        return $this->_messageFactory;
    }

    /**
     * @return \Magento_Core_Controller_Request_Http
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * @return \Magento_Core_Model_StoreManager
     */
    public function getStoreManager()
    {
        return $this->_storeManager;
    }

    /**
     * @return \Magento_Core_Model_Url
     */
    public function getUrl()
    {
        return $this->_url;
    }
}
