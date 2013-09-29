<?php
/**
 * Abstract helper context
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Core_Helper_Context implements Magento_ObjectManager_ContextInterface
{
    /**
     * @var Magento_Core_Model_Translate
     */
    protected $_translator;

    /**
     * @var Magento_Core_Model_ModuleManager
     */
    protected $_moduleManager;

    /** 
     * @var  Magento_Core_Model_Event_Manager 
     */
    protected $_eventManager;

    /**
     * @var Magento_Core_Model_Logger
     */
    protected $_logger;

    /**
     * @var Magento_Core_Controller_Request_HttpProxy
     */
    protected $_httpRequest;

    /**
     * @var Magento_Core_Model_Cache_Config
     */
    protected $_cacheConfig;

    /**
     * @var Magento_Core_Model_EncryptionFactory
     */
    protected $_encryptorFactory;

    /**
     * @var Magento_Core_Model_Fieldset_Config
     */
    protected $_fieldsetConfig;

    /**
     * @var Magento_Core_Model_App
     */
    protected $_app;

    /**
     * @var Magento_Core_Model_Url
     */
    protected $_urlFactory;

    /**
     * @var Magento_Core_Model_Url
     */
    protected $_urlModel;

    /**
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Translate $translator
     * @param Magento_Core_Model_ModuleManager $moduleManager
     * @param Magento_Core_Controller_Request_HttpProxy $httpRequest
     * @param Magento_Core_Model_Cache_Config $cacheConfig
     * @param Magento_Core_Model_EncryptionFactory $encryptorFactory
     * @param Magento_Core_Model_Fieldset_Config $fieldsetConfig
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_App $app
     * @param Magento_Core_Model_UrlFactory $urlFactory
     * @param Magento_Core_Model_Url $urlModel
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_Translate $translator,
        Magento_Core_Model_ModuleManager $moduleManager,
        Magento_Core_Controller_Request_HttpProxy $httpRequest,
        Magento_Core_Model_Cache_Config $cacheConfig,
        Magento_Core_Model_EncryptionFactory $encryptorFactory,
        Magento_Core_Model_Fieldset_Config $fieldsetConfig,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_App $app,
        Magento_Core_Model_UrlFactory $urlFactory,
        Magento_Core_Model_Url $urlModel
    ) {
        $this->_translator = $translator;
        $this->_moduleManager = $moduleManager;
        $this->_httpRequest = $httpRequest;
        $this->_cacheConfig = $cacheConfig;
        $this->_encryptorFactory = $encryptorFactory;
        $this->_fieldsetConfig = $fieldsetConfig;
        $this->_eventManager = $eventManager;
        $this->_logger = $logger;
        $this->_app = $app;
        $this->_urlFactory = $urlFactory;
        $this->_urlModel = $urlModel;
    }

    /**
     * @return Magento_Core_Model_Translate
     */
    public function getTranslator()
    {
        return $this->_translator;
    }

    /**
     * @return Magento_Core_Model_ModuleManager
     */
    public function getModuleManager()
    {
        return $this->_moduleManager;
    }

    /**
     * @return Magento_Core_Model_App
     */
    public function getApp()
    {
        return $this->_app;
    }

    /**
     * @return Magento_Core_Model_Url
     */
    public function getUrlFactory()
    {
        return $this->_urlFactory;
    }

    /**
     * @return Magento_Core_Model_Url
     */
    public function getUrlModel()
    {
        return $this->_urlModel;
    }

    /**
     * @return Magento_Core_Controller_Request_HttpProxy
     */
    public function getRequest()
    {
        return $this->_httpRequest;
    }

    /**
     * @return Magento_Core_Model_Cache_Config
     */
    public function getCacheConfig()
    {
        return $this->_cacheConfig;
    }

    /**
     * @return Magento_Core_Model_EncryptionFactory
     */
    public function getEncryptorFactory()
    {
        return $this->_encryptorFactory;
    }

    /**
     * @return Magento_Core_Model_Event_Manager
     */
    public function getEventManager()
    {
        return $this->_eventManager;
    }

    /**
     * @return Magento_Core_Model_Fieldset_Config
     */
    public function getFieldsetConfig()
    {
        return $this->_fieldsetConfig;
    }
    
    /**
     * @return Magento_Core_Model_Logger
     */
    public function getLogger()
    {
        return $this->_logger;
    }
}
