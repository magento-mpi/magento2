<?php
/**
 * Abstract helper context
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\App\Helper;

class Context implements \Magento\ObjectManager\ContextInterface
{
    /**
     * @var \Magento\TranslateInterface
     */
    protected $_inlineFactory;

    /**
     * @var \Magento\Module\Manager
     */
    protected $_moduleManager;

    /**
     * @var  \Magento\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\Logger
     */
    protected $_logger;

    /**
     * @var \Magento\App\RequestInterface
     */
    protected $_httpRequest;

    /**
     * @var \Magento\Cache\ConfigInterface
     */
    protected $_cacheConfig;

    /**
     * @var \Magento\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\HTTP\Header
     */
    protected $_httpHeader;

    /**
     * @var \Magento\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $_remoteAddress;

    /**
     * @param \Magento\Logger $logger
     * @param \Magento\Translate\InlineFactory $inlineFactory
     * @param \Magento\Module\Manager $moduleManager
     * @param \Magento\App\RequestInterface $httpRequest
     * @param \Magento\Cache\ConfigInterface $cacheConfig
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\UrlInterface $urlBuilder
     * @param \Magento\HTTP\Header $httpHeader
     * @param \Magento\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Logger $logger,
        \Magento\Translate\InlineFactory $inlineFactory,
        \Magento\Module\Manager $moduleManager,
        \Magento\App\RequestInterface $httpRequest,
        \Magento\Cache\ConfigInterface $cacheConfig,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\UrlInterface $urlBuilder,
        \Magento\HTTP\Header $httpHeader,
        \Magento\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
    ) {
        $this->_inlineFactory = $inlineFactory;
        $this->_moduleManager = $moduleManager;
        $this->_httpRequest = $httpRequest;
        $this->_cacheConfig = $cacheConfig;
        $this->_eventManager = $eventManager;
        $this->_logger = $logger;
        $this->_urlBuilder = $urlBuilder;
        $this->_httpHeader = $httpHeader;
        $this->_remoteAddress = $remoteAddress;
    }

    /**
     * @return \Magento\Translate\InlineFactory
     */
    public function getInlineFactory()
    {
        return $this->_inlineFactory;
    }

    /**
     * @return \Magento\Module\Manager
     */
    public function getModuleManager()
    {
        return $this->_moduleManager;
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
     * @return \Magento\Cache\ConfigInterface
     */
    public function getCacheConfig()
    {
        return $this->_cacheConfig;
    }

    /**
     * @return \Magento\Event\ManagerInterface
     */
    public function getEventManager()
    {
        return $this->_eventManager;
    }

    /**
     * @return \Magento\Logger
     */
    public function getLogger()
    {
        return $this->_logger;
    }

    /**
     * @return \Magento\HTTP\Header
     */
    public function getHttpHeader()
    {
        return $this->_httpHeader;
    }

    /**
     * @return \Magento\HTTP\PhpEnvironment\RemoteAddress
     */
    public function getRemoteAddress()
    {
        return $this->_remoteAddress;
    }
}
