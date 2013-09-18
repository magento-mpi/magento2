<?php
/**
 * Abstract helper context
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Core\Helper;

class Context implements \Magento\ObjectManager\ContextInterface
{
    /**
     * @var \Magento\Core\Model\Translate
     */
    protected $_translator;

    /**
     * @var \Magento\Core\Model\ModuleManager
     */
    protected $_moduleManager;

    /** 
     * @var  Magento_Core_Model_Event_Manager 
     */
    protected $_eventManager;

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
     * @param \Magento\Core\Model\Translate $translator
     * @param \Magento\Core\Model\ModuleManager $moduleManager
     * @param Magento_Core_Controller_Request_HttpProxy $httpRequest
     * @param Magento_Core_Model_Cache_Config $cacheConfig
     * @param Magento_Core_Model_EncryptionFactory $encyptorFactory
     * @param Magento_Core_Model_Fieldset_Config $fieldsetConfig
     * @param \Magento\Core\Model\Event\Manager $eventManager
     */
    public function __construct(
        \Magento\Core\Model\Translate $translator,
        \Magento\Core\Model\ModuleManager $moduleManager,
        Magento_Core_Controller_Request_HttpProxy $httpRequest,
        Magento_Core_Model_Cache_Config $cacheConfig,
        Magento_Core_Model_EncryptionFactory $encyptorFactory,
        Magento_Core_Model_Fieldset_Config $fieldsetConfig,
        \Magento\Core\Model\Event\Manager $eventManager
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
     * @return \Magento\Core\Model\Translate
     */
    public function getTranslator()
    {
        return $this->_translator;
    }

    /**
     * @return \Magento\Core\Model\ModuleManager
     */
    public function getModuleManager()
    {
        return $this->_moduleManager;
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
}
