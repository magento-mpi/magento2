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
     * @var  \Magento\Core\Model\Event\Manager 
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
     * @var \Magento\Core\Model\Cache\Config
     */
    protected $_cacheConfig;

    /**
     * @var \Magento\Core\Model\EncryptionFactory
     */
    protected $_encryptorFactory;

    /**
     * @var \Magento\Core\Model\Fieldset\Config
     */
    protected $_fieldsetConfig;
    
    /**
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Translate $translator
     * @param \Magento\Core\Model\ModuleManager $moduleManager
     * @param \Magento\Core\Controller\Request\HttpProxy $httpRequest
     * @param \Magento\Core\Model\Cache\Config $cacheConfig
     * @param \Magento\Core\Model\EncryptionFactory $encyptorFactory
     * @param \Magento\Core\Model\Fieldset\Config $fieldsetConfig
     * @param \Magento\Core\Model\Event\Manager $eventManager
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        \Magento\Core\Model\Translate $translator,
        \Magento\Core\Model\ModuleManager $moduleManager,
        \Magento\Core\Controller\Request\HttpProxy $httpRequest,
        \Magento\Core\Model\Cache\Config $cacheConfig,
        \Magento\Core\Model\EncryptionFactory $encyptorFactory,
        \Magento\Core\Model\Fieldset\Config $fieldsetConfig,
        \Magento\Core\Model\Event\Manager $eventManager
    ) {
        $this->_translator = $translator;
        $this->_moduleManager = $moduleManager;
        $this->_httpRequest = $httpRequest;
        $this->_cacheConfig = $cacheConfig;
        $this->_encryptorFactory = $encyptorFactory;
        $this->_fieldsetConfig = $fieldsetConfig;
        $this->_eventManager = $eventManager;
        $this->_logger = $logger;
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
     * @return \Magento\Core\Controller\Request\HttpProxy
     */
    public function getRequest()
    {
        return $this->_httpRequest;
    }

    /**
     * @return \Magento\Core\Model\Cache\Config
     */
    public function getCacheConfig()
    {
        return $this->_cacheConfig;
    }

    /**
     * @return \Magento\Core\Model\EncryptionFactory
     */
    public function getEncryptorFactory()
    {
        return $this->_encryptorFactory;
    }

    /**
     * @return \Magento\Core\Model\Event\Manager
     */
    public function getEventManager()
    {
        return $this->_eventManager;
    }

    /**
     * @return \Magento\Core\Model\Fieldset\Config
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
