<?php
/**
 * Abstract helper context
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Framework\App\Helper;

class Context implements \Magento\Framework\ObjectManager\ContextInterface
{
    /**
     * @var \Magento\Framework\Translate\InlineInterface
     */
    protected $translateInline;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    /**
     * @var  \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\Framework\Logger
     */
    protected $_logger;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_httpRequest;

    /**
     * @var \Magento\Framework\Cache\ConfigInterface
     */
    protected $_cacheConfig;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\Framework\HTTP\Header
     */
    protected $_httpHeader;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $_remoteAddress;

    /**
     * @param \Magento\Framework\Logger $logger
     * @param \Magento\Framework\Translate\InlineInterface $translateInline
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Framework\App\RequestInterface $httpRequest
     * @param \Magento\Framework\Cache\ConfigInterface $cacheConfig
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\HTTP\Header $httpHeader
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Logger $logger,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\RequestInterface $httpRequest,
        \Magento\Framework\Cache\ConfigInterface $cacheConfig,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\HTTP\Header $httpHeader,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
    ) {
        $this->translateInline = $translateInline;
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
     * @return \Magento\Framework\Translate\InlineInterface
     */
    public function getTranslateInline()
    {
        return $this->translateInline;
    }

    /**
     * @return \Magento\Framework\Module\Manager
     */
    public function getModuleManager()
    {
        return $this->_moduleManager;
    }

    /**
     * @return \Magento\Framework\UrlInterface
     */
    public function getUrlBuilder()
    {
        return $this->_urlBuilder;
    }

    /**
     * @return \Magento\Framework\App\RequestInterface
     */
    public function getRequest()
    {
        return $this->_httpRequest;
    }

    /**
     * @return \Magento\Framework\Cache\ConfigInterface
     */
    public function getCacheConfig()
    {
        return $this->_cacheConfig;
    }

    /**
     * @return \Magento\Framework\Event\ManagerInterface
     */
    public function getEventManager()
    {
        return $this->_eventManager;
    }

    /**
     * @return \Magento\Framework\Logger
     */
    public function getLogger()
    {
        return $this->_logger;
    }

    /**
     * @return \Magento\Framework\HTTP\Header
     */
    public function getHttpHeader()
    {
        return $this->_httpHeader;
    }

    /**
     * @return \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    public function getRemoteAddress()
    {
        return $this->_remoteAddress;
    }
}
