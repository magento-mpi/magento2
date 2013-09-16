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
     * @var Magento_Core_Controller_Request_Http
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
     * @param Magento_Core_Model_Translate $translator
     * @param Magento_Core_Model_ModuleManager $moduleManager
     * @param Magento_Core_Controller_Request_Http $httpRequest
     * @param Magento_Core_Model_Cache_Config $cacheConfig
     * @param Magento_Core_Model_EncryptionFactory $encyptorFactory
     * @param Magento_Core_Model_Fieldset_Config $fieldsetConfig
     * @param Magento_Core_Model_Event_Manager $eventManager
     */
    public function __construct(
        Magento_Core_Model_Translate $translator,
        Magento_Core_Model_ModuleManager $moduleManager,
        Magento_Core_Controller_Request_Http $httpRequest,
        Magento_Core_Model_Cache_Config $cacheConfig,
        Magento_Core_Model_EncryptionFactory $encyptorFactory,
        Magento_Core_Model_Fieldset_Config $fieldsetConfig,
        Magento_Core_Model_Event_Manager $eventManager
    ) {
        $this->_translator = $translator;
        $this->_moduleManager = $moduleManager;
        $this->_httpRequest = $httpRequest;
        $this->_cacheConfig = $cacheConfig;
        $this->_encryptorFactory = $encyptorFactory;
        $this->_fieldsetConfig = $fieldsetConfig;
        $this->_eventManager = $eventManager;
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
     * @return Magento_Core_Controller_Request_Http
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
}
