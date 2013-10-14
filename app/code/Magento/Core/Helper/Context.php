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
     * @var  \Magento\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\Core\Model\Logger
     */
    protected $_logger;

    /**
     * @var \Magento\App\RequestInterface
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
     * @var \Magento\Core\Model\App
     */
    protected $_app;

    /**
     * @var \Magento\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Core\Model\Translate $translator
     * @param \Magento\Core\Model\ModuleManager $moduleManager
     * @param \Magento\App\RequestInterface $httpRequest
     * @param \Magento\Core\Model\Cache\Config $cacheConfig
     * @param \Magento\Core\Model\EncryptionFactory $encryptorFactory
     * @param \Magento\Core\Model\Fieldset\Config $fieldsetConfig
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Core\Model\App $app
     * @param \Magento\UrlInterface $urlBuilder
     */
    public function __construct(
        \Magento\Core\Model\Logger $logger,
        \Magento\Core\Model\Translate $translator,
        \Magento\Core\Model\ModuleManager $moduleManager,
        \Magento\App\RequestInterface $httpRequest,
        \Magento\Core\Model\Cache\Config $cacheConfig,
        \Magento\Core\Model\EncryptionFactory $encryptorFactory,
        \Magento\Core\Model\Fieldset\Config $fieldsetConfig,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Core\Model\App $app,
        \Magento\UrlInterface $urlBuilder
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
        $this->_urlBuilder = $urlBuilder;
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
     * @return \Magento\Core\Model\App
     */
    public function getApp()
    {
        return $this->_app;
    }

    /**
     * @return \Magento\UrlInterface
     */
    public function getUrlBuilder()
    {
        return $this->_urlBuilder;
    }

    /**
     * @return \Magento\App\RequestInterface
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
     * @return \Magento\Event\ManagerInterface
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
     * @return \Magento\Core\Model\Logger
     */
    public function getLogger()
    {
        return $this->_logger;
    }
}
