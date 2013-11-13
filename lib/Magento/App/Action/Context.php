<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Action;

class Context implements \Magento\ObjectManager\ContextInterface
{
    /**
     * @var \Magento\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\App\ResponseInterface
     */
    protected $_response;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\App\FrontController
     */
    protected $_frontController = null;

    /**
     * @var \Magento\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\Logger
     */
    protected $_logger;

    /**
     * @var \Magento\App\ActionFlag
     */
    protected $_flag;

    /** @var \Magento\Encryption\UrlCoder */
    protected $_urlCoder;

    /** @var \Magento\HTTP\Url */
    protected $_httpUrl;

    /**
     * @var \Magento\Core\Model\Translate
     */
    protected $_translator;

    /**
     * @var \Magento\HTTP\Authentication
     */
    protected $authentication;

    /**
     * @param \Magento\Logger $logger
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\App\ResponseInterface $response
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\App\FrontController $frontController
     * @param \Magento\View\LayoutInterface $layout
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\App\State $appState
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Config\ScopeInterface $configScope
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Core\Model\Session\AbstractSession $session
     * @param \Magento\Core\Model\Url $url
     * @param \Magento\Core\Model\Translate $translator
     * @param \Magento\Core\Model\Store\Config $storeConfig
     * @param \Magento\Core\Model\Cookie $cookie
     * @param \Magento\Core\Model\App $app
     * @param \Magento\Core\Helper\AbstractHelper $helper
     * @param \Magento\App\ActionFlag $flag
     * @param \Magento\Encryption\UrlCoder $urlCoder
     * @param \Magento\HTTP\Url $httpUrl
     * @param \Magento\HTTP\Authentication $authentication
     */
    public function __construct(
        \Magento\Logger $logger,
        \Magento\App\RequestInterface $request,
        \Magento\App\ResponseInterface $response,
        \Magento\ObjectManager $objectManager,
        \Magento\App\FrontController $frontController,
        \Magento\View\LayoutInterface $layout,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\App\State $appState,
        \Magento\Filesystem $filesystem,
        \Magento\Config\ScopeInterface $configScope,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Core\Model\Session\AbstractSession $session,
        \Magento\Core\Model\Url $url,
        \Magento\Core\Model\Translate $translator,
        \Magento\Core\Model\Store\Config $storeConfig,
        \Magento\Core\Model\Cookie $cookie,
        \Magento\Core\Model\App $app,
        \Magento\Core\Helper\AbstractHelper $helper,
        \Magento\App\ActionFlag $flag,
        \Magento\Encryption\UrlCoder $urlCoder,
        \Magento\HTTP\Url $httpUrl,
        \Magento\HTTP\Authentication $authentication
    ) {
        $this->_request = $request;
        $this->_response = $response;
        $this->_objectManager = $objectManager;
        $this->_frontController = $frontController;
        $this->_layout = $layout;
        $this->_eventManager = $eventManager;
        $this->_logger = $logger;
        $this->_appState = $appState;
        $this->_filesystem = $filesystem;
        $this->_configScope = $configScope;
        $this->_storeManager = $storeManager;
        $this->_locale = $locale;
        $this->_session = $session;
        $this->_url = $url;
        $this->_translator = $translator;
        $this->_storeConfig = $storeConfig;
        $this->_cookie = $cookie;
        $this->_app = $app;
        $this->_helper = $helper;
        $this->_urlCoder = $urlCoder;
        $this->_httpUrl = $httpUrl;
        $this->_flag = $flag;
        $this->authentication = $authentication;
    }

    /**
     * @return \Magento\App\ResponseInterface
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * @return \Magento\Core\Model\App
     */
    public function getApp()
    {
        return $this->_app;
    }

    /**
     * @return \Magento\App\State
     */
    public function getAppState()
    {
        return $this->_appState;
    }


    /**
     * @return \Magento\Config\ScopeInterface
     */
    public function getConfigScope()
    {
        return $this->_configScope;
    }

    /**
     * @return \Magento\Core\Model\Cookie
     */
    public function getCookie()
    {
        return $this->_cookie;
    }

    /**
     * @return \Magento\Event\ManagerInterface
     */
    public function getEventManager()
    {
        return $this->_eventManager;
    }

    /**
     * @return \Magento\Filesystem
     */
    public function getFilesystem()
    {
        return $this->_filesystem;
    }

    /**
     * @return \Magento\App\ActionFlag
     */
    public function getFlag()
    {
        return $this->_flag;
    }

    /**
     * @return \Magento\App\FrontController
     */
    public function getFrontController()
    {
        return $this->_frontController;
    }

    /**
     * @return \Magento\Core\Helper\AbstractHelper
     */
    public function getHelper()
    {
        return $this->_helper;
    }

    /**
     * @return \Magento\View\LayoutInterface
     */
    public function getLayout()
    {
        return $this->_layout;
    }

    /**
     * @return \Magento\Core\Model\LocaleInterface
     */
    public function getLocale()
    {
        return $this->_locale;
    }

    /**
     * @return \Magento\Logger
     */
    public function getLogger()
    {
        return $this->_logger;
    }

    /**
     * @return \Magento\ObjectManager
     */
    public function getObjectManager()
    {
        return $this->_objectManager;
    }

    /**
     * @return \Magento\App\RequestInterface
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * @return \Magento\Core\Model\Session\AbstractSession
     */
    public function getSession()
    {
        return $this->_session;
    }

    /**
     * @return \Magento\Core\Model\Store\Config
     */
    public function getStoreConfig()
    {
        return $this->_storeConfig;
    }

    /**
     * @return \Magento\Core\Model\StoreManagerInterface
     */
    public function getStoreManager()
    {
        return $this->_storeManager;
    }

    /**
     * @return \Magento\Core\Model\Translate
     */
    public function getTranslate()
    {
        return $this->_translator;
    }

    /**
     * @return \Magento\Core\Model\Url
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * @return \Magento\Encryption\UrlCoder
     */
    public function getUrlCoder()
    {
        return $this->_urlCoder;
    }

    public function getHttpUrl()
    {
        return $this->_httpUrl;
    }

    /**
     * @return \Magento\HTTP\Authentication
     */
    public function getAuthentication()
    {
        return $this->authentication;
    }
}
