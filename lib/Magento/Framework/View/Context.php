<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View;

use Magento\Framework\App\Request\Http as Request;
use Magento\Framework\App\FrontControllerInterface;
use Magento\Framework\TranslateInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Url as ViewUrl;
use Magento\Framework\View\ConfigInterface as ViewConfig;
use Magento\Logger;
use Magento\Framework\App\State as AppState;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\Session\SessionManager;
use Magento\Framework\App\CacheInterface as Cache;
use Magento\Framework\App\Cache\StateInterface as CacheState;
use Magento\UrlInterface;
use Magento\Framework\Event\ManagerInterface;

/**
 * Application Runtime Context
 *
 * @todo Reduce fields number
 * @todo Reduce class dependencies
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Context
{
    /**
     * Request
     *
     * @var Request
     */
    protected $request;

    /**
     * Event manager
     *
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * URL builder
     * @var \Magento\UrlInterface
     */
    protected $urlBuilder;

    /**
     * Translator
     *
     * @var \Magento\Framework\TranslateInterface
     */
    protected $translator;

    /**
     * Cache
     *
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $cache;

    /**
     * Design
     *
     * @var \Magento\Framework\View\DesignInterface
     */
    protected $design;

    /**
     * Session
     *
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $session;

    /**
     * Store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Front controller
     *
     * @var FrontControllerInterface
     */
    protected $frontController;

    /**
     * View URL
     *
     * @var \Magento\Framework\View\Url
     */
    protected $viewUrl;

    /**
     * Layout
     *
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /**
     * View config model
     *
     * @var \Magento\Framework\View\Config
     */
    protected $viewConfig;

    /**
     * Cache state
     *
     * @var \Magento\Framework\App\Cache\StateInterface
     */
    protected $cacheState;

    /**
     * Logger
     *
     * @var \Magento\Logger
     */
    protected $logger;

    /**
     * Application state
     *
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * Constructor
     *
     * @param Request $request
     * @param ManagerInterface $eventManager
     * @param UrlInterface $urlBuilder
     * @param TranslateInterface $translator
     * @param Cache $cache
     * @param DesignInterface $design
     * @param SessionManager $session
     * @param ScopeConfigInterface $scopeConfig
     * @param FrontControllerInterface $frontController
     * @param ViewUrl $viewUrl
     * @param ViewConfig $viewConfig
     * @param CacheState $cacheState
     * @param Logger $logger
     * @param AppState $appState
     * @param LayoutInterface $layout
     *
     * @todo reduce parameter number
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Request $request,
        ManagerInterface $eventManager,
        UrlInterface $urlBuilder,
        TranslateInterface $translator,
        Cache $cache,
        DesignInterface $design,
        SessionManager $session,
        ScopeConfigInterface $scopeConfig,
        FrontControllerInterface $frontController,
        ViewUrl $viewUrl,
        ViewConfig $viewConfig,
        CacheState $cacheState,
        Logger $logger,
        AppState $appState,
        LayoutInterface $layout
    ) {
        $this->request = $request;
        $this->eventManager = $eventManager;
        $this->urlBuilder = $urlBuilder;
        $this->translator = $translator;
        $this->cache = $cache;
        $this->design = $design;
        $this->session = $session;
        $this->scopeConfig = $scopeConfig;
        $this->frontController = $frontController;
        $this->viewUrl         = $viewUrl;
        $this->viewConfig      = $viewConfig;
        $this->cacheState      = $cacheState;
        $this->logger          = $logger;
        $this->appState        = $appState;
        $this->layout          = $layout;
    }

    /**
     * Retrieve cache
     *
     * @return \Magento\Framework\App\CacheInterface
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Retrieve design package
     *
     * @return \Magento\Framework\View\DesignInterface
     */
    public function getDesignPackage()
    {
        return $this->design;
    }

    /**
     * Retrieve event manager
     *
     * @return ManagerInterface
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

    /**
     * Retrieve front controller
     *
     * @return FrontControllerInterface
     */
    public function getFrontController()
    {
        return $this->frontController;
    }

    /**
     * Retrieve layout
     *
     * @return \Magento\Framework\View\LayoutInterface
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Retrieve request
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Retrieve session
     *
     * @return \Magento\Framework\Session\SessionManagerInterface
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Retrieve scope config
     *
     * @return \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public function getScopeConfig()
    {
        return $this->scopeConfig;
    }

    /**
     * Retrieve translator
     *
     * @return \Magento\Framework\TranslateInterface
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * Retrieve URL builder
     *
     * @return \Magento\UrlInterface
     */
    public function getUrlBuilder()
    {
        return $this->urlBuilder;
    }

    /**
     * Retrieve View URL
     *
     * @return \Magento\Framework\View\Url
     */
    public function getViewUrl()
    {
        return $this->viewUrl;
    }

    /**
     * Retrieve view config
     *
     * @return \Magento\Framework\View\ConfigInterface
     */
    public function getViewConfig()
    {
        return $this->viewConfig;
    }

    /**
     * Retrieve cache state
     *
     * @return \Magento\Framework\App\Cache\StateInterface
     */
    public function getCacheState()
    {
        return $this->cacheState;
    }

    /**
     * Retrieve logger
     *
     * @return \Magento\Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Retrieve layout area
     *
     * @return string
     */
    public function getArea()
    {
        return $this->appState->getAreaCode();
    }

    /**
     * Retrieve the module name
     *
     * @return string
     */
    public function getModuleName()
    {
        return $this->getRequest()->getModuleName();
    }

    /**
     * Retrieve the module name
     *
     * @return string
     *
     * @todo alias of getModuleName
     */
    public function getFrontName()
    {
        return $this->getRequest()->getModuleName();
    }

    /**
     * Retrieve the controller name
     *
     * @return string
     */
    public function getControllerName()
    {
        return $this->getRequest()->getControllerName();
    }

    /**
     * Retrieve the action name
     *
     * @return string
     */
    public function getActionName()
    {
        return $this->getRequest()->getActionName();
    }

    /**
     * Retrieve the full action name
     *
     * @return string
     */
    public function getFullActionName()
    {
        return strtolower($this->getFrontName() . '_' . $this->getControllerName() . '_' . $this->getActionName());
    }

    /**
     * Retrieve acceptance type
     *
     * @return string
     */
    public function getAcceptType()
    {
        // TODO: do intelligence here
        $type = $this->getHeader('Accept', 'html');
        if (strpos($type, 'json') !== false) {
            return 'json';
        } elseif (strpos($type, 'soap') !== false) {
            return 'soap';
        } elseif (strpos($type, 'text/html') !== false) {
            return 'html';
        } else {
            return 'xml';
        }
    }

    /**
     * Retrieve a member of the $_POST superglobal
     *
     * @param string $key
     * @param mixed $default Default value to use if key not found
     * @return mixed|null if key does not exist
     */
    public function getPost($key = null, $default = null)
    {
        return $this->getRequest()->getPost($key, $default);
    }

    /**
     * Retrieve a member of the $_POST superglobal
     *
     * @param string|null $key
     * @param mixed $default Default value to use if key not found
     * @return mixed alias of getPost
     */
    public function getQuery($key = null, $default = null)
    {
        return $this->getRequest()->getPost($key, $default);
    }

    /**
     * Retrieve a parameter
     *
     * @param string|null $key
     * @param mixed $default Default value to use if key not found
     * @return mixed
     */
    public function getParam($key = null, $default = null)
    {
        return $this->getRequest()->getParam($key, $default);
    }

    /**
     * Retrieve an array of parameters
     *
     * @return array
     */
    public function getParams()
    {
        return $this->getRequest()->getParams();
    }

    /**
     * Return the value of the given HTTP header.
     *
     * @param string $header
     * @return string|false HTTP header value, or false if not found
     */
    public function getHeader($header)
    {
        return $this->getRequest()->getHeader($header);
    }

    /**
     * Return the raw body of the request, if present
     *
     * @return string|false Raw body, or false if not present
     */
    public function getRawBody()
    {
        return $this->getRequest()->getRawBody();
    }

    /**
     * Retrieve application state
     *
     * @return \Magento\Framework\App\State
     */
    public function getAppState()
    {
        return $this->appState;
    }

    /**
     * Retrieve design theme instance
     *
     * @return Design\ThemeInterface
     */
    public function getDesignTheme()
    {
        $theme = $this->design->getDesignTheme();
        $theme->setCode('Magento/plushe');
        $theme->setThemePath('Magento/plushe');
        $theme->setId(8);

        return $this->getPhysicalTheme($theme);
    }

    /**
     * Retrieve parent theme instance
     *
     * @param Design\ThemeInterface $theme
     * @return Design\ThemeInterface
     * @throws \Exception
     */
    protected function getPhysicalTheme(Design\ThemeInterface $theme)
    {
        $result = $theme;
        while ($result->getId() && !$result->isPhysical()) {
            $result = $result->getParentTheme();
        }
        if (!$result) {
            throw new \Exception("Unable to find a physical ancestor for a theme '{$theme->getThemeTitle()}'.");
        }
        return $result;
    }
}
