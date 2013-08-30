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
     * @param Magento_Core_Model_Translate $translator
     * @param Magento_Core_Model_ModuleManager $moduleManager
     * @param Magento_Core_Controller_Request_Http $httpRequest
     * @param Magento_Core_Model_Cache_Config $cacheConfig
     * @param Magento_Core_Model_EncryptionFactory $encyptorFactory
     */
    public function __construct(
        Magento_Core_Model_Translate $translator,
        Magento_Core_Model_ModuleManager $moduleManager,
        Magento_Core_Controller_Request_Http $httpRequest,
        Magento_Core_Model_Cache_Config $cacheConfig,
        Magento_Core_Model_EncryptionFactory $encyptorFactory
    ) {
        $this->_translator = $translator;
        $this->_moduleManager = $moduleManager;
        $this->_httpRequest = $httpRequest;
        $this->_cacheConfig = $cacheConfig;
        $this->_encryptorFactory = $encyptorFactory;
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
}
