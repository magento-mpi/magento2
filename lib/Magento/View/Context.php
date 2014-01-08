<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

use Magento\App\Request\Http as Request;
use Magento\App\FrontControllerInterface;

use Magento\TranslateInterface;
use Magento\Core\Model\Store\Config as StoreConfig;
use Magento\View\Url as ViewUrl;
use Magento\View\ConfigInterface as ViewConfig;
use Magento\Logger;
use Magento\Core\Model\App;
use Magento\App\State as AppState;

use Magento\Session\SessionManager;
use Magento\App\CacheInterface as Cache;
use Magento\App\Cache\StateInterface as CacheState;
use Magento\UrlInterface;
use Magento\Event\ManagerInterface;

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
     * @var Request
     */
    protected $request;

    /**
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Magento\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\TranslateInterface
     */
    protected $translator;

    /**
     * @var \Magento\App\CacheInterface
     */
    protected $cache;

    /**
     * @var \Magento\View\DesignInterface
     */
    protected $design;

    /**
     * @var \Magento\Session\SessionManagerInterface
     */
    protected $session;

    /**
     * @var \Magento\Core\Model\Store\Config
     */
    protected $storeConfig;

    /**
     * @var FrontControllerInterface
     */
    protected $frontController;

    /**
     * @var \Magento\View\Url
     */
    protected $viewUrl;

    /**
     * @var \Magento\View\LayoutInterface
     */
    protected $layout;

    /**
     * View config model
     *
     * @var \Magento\View\Config
     */
    protected $viewConfig;

    /**
     * @var \Magento\App\Cache\StateInterface
     */
    protected $cacheState;

    /**
     * @var \Magento\Logger
     */
    protected $logger;

    /**
     * @var \Magento\Core\Model\App
     */
    protected $app;

    /**
     * @var \Magento\App\State
     */
    protected $appState;

    /**
     * @param Request $request
     * @param ManagerInterface $eventManager
     * @param UrlInterface $urlBuilder
     * @param TranslateInterface $translator
     * @param Cache $cache
     * @param DesignInterface $design
     * @param SessionManager $session
     * @param StoreConfig $storeConfig
     * @param FrontControllerInterface $frontController
     * @param ViewUrl $viewUrl
     * @param ViewConfig $viewConfig
     * @param CacheState $cacheState
     * @param Logger $logger
     * @param App $app
     * @param AppState $appState
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
        StoreConfig $storeConfig,
        FrontControllerInterface $frontController,
        ViewUrl $viewUrl,
        ViewConfig $viewConfig,
        CacheState $cacheState,
        Logger $logger,
        App $app,
        AppState $appState
    ) {
        $this->request         = $request;
        $this->eventManager    = $eventManager;
        $this->urlBuilder      = $urlBuilder;
        $this->translator      = $translator;
        $this->cache           = $cache;
        $this->design          = $design;
        $this->session         = $session;
        $this->storeConfig     = $storeConfig;
        $this->frontController = $frontController;
        $this->viewUrl         = $viewUrl;
        $this->viewConfig      = $viewConfig;
        $this->cacheState      = $cacheState;
        $this->logger          = $logger;
        $this->app             = $app;
        $this->appState        = $appState;
    }

    /**
     * @return \Magento\App\CacheInterface
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @return \Magento\View\DesignInterface
     */
    public function getDesignPackage()
    {
        return $this->design;
    }

    /**
     * @return ManagerInterface
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

    /**
     * @return FrontControllerInterface
     */
    public function getFrontController()
    {
        return $this->frontController;
    }

    /**
     * @return \Magento\View\LayoutInterface
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return \Magento\Session\SessionManagerInterface
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @return \Magento\Core\Model\Store\Config
     */
    public function getStoreConfig()
    {
        return $this->storeConfig;
    }

    /**
     * @return \Magento\TranslateInterface
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @return \Magento\UrlInterface
     */
    public function getUrlBuilder()
    {
        return $this->urlBuilder;
    }

    /**
     * @return \Magento\View\Url
     */
    public function getViewUrl()
    {
        return $this->viewUrl;
    }

    /**
     * @return \Magento\View\ConfigInterface
     */
    public function getViewConfig()
    {
        return $this->viewConfig;
    }

    /**
     * @return \Magento\App\Cache\StateInterface
     */
    public function getCacheState()
    {
        return $this->cacheState;
    }

    /**
     * @return \Magento\Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @return \Magento\Core\Model\App
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * Retrieve layout area
     *
     * @return string
     */
    public function getArea()
    {
        return $this->app->getLayout()->getArea();
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
     * @return mixed Returns null if key does not exist
     */
    public function getPost($key = null, $default = null)
    {
        return $this->getRequest()->getPost($key, $default);
    }

    /**
     * Retrieve a member of the $_POST superglobal
     *
     * @param string $key
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
     * @param mixed $key
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
     * @param $header
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
     * @return \Magento\App\State
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
        $theme->setCode('magento_plushe');
        $theme->setThemePath('magento_plushe');
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
