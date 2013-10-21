<?php
/**
 * Application Runtime Context
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

use Magento\Core\Controller\Request\Http as Request;
use Magento\Core\Controller\Varien\Front;

use Magento\Core\Model\Event\Manager as EventManager;
use Magento\Core\Model\Translate;
use Magento\Core\Model\Store\Config as StoreConfig;
use Magento\Core\Model\Factory\Helper as FactoryHelper;
use Magento\Core\Model\View\Url as ViewUrl;
use Magento\Core\Model\View\Config as ViewConfig;
use Magento\Core\Model\Logger;
use Magento\Core\Model\App;
use Magento\Core\Model\App\State as AppState;

use Magento\Core\Model\Session\AbstractSession;
use Magento\Core\Model\UrlInterface;
use Magento\Core\Model\CacheInterface as Cache;
use Magento\Core\Model\Cache\StateInterface as CacheState;

use Magento\View\DesignInterface;

/**
 * @todo Reduce fields number
 * @todo Reduce class dependencies
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Context
{
    /**
     * @var \Magento\Core\Controller\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Core\Model\Event\Manager
     */
    protected $eventManager;

    /**
     * @var \Magento\Core\Model\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Core\Model\Translate
     */
    protected $translator;

    /**
     * @var \Magento\Core\Model\CacheInterface
     */
    protected $cache;

    /**
     * @var \Magento\Core\Model\View\Design
     */
    protected $design;

    /**
     * @var \Magento\Core\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Core\Model\Store\Config
     */
    protected $storeConfig;

    /**
     * @var \Magento\Core\Controller\Varien\Front
     */
    protected $frontController;

    /**
     * @var \Magento\Core\Model\Factory\Helper
     */
    protected $helperFactory;

    /**
     * @var \Magento\Core\Model\View\Url
     */
    protected $viewUrl;

    /**
     * View config model
     *
     * @var \Magento\Core\Model\View\Config
     */
    protected $viewConfig;

    /**
     * @var \Magento\Core\Model\Cache\StateInterface
     */
    protected $cacheState;

    /**
     * @var \Magento\Core\Model\Logger
     */
    protected $logger;

    /**
     * @var \Magento\Core\Model\App
     */
    protected $app;

    /**
     * @var \Magento\Core\Model\App\State
     */
    protected $appState;

    /**
     * @param Request $request
     * @param EventManager $eventManager
     * @param UrlInterface $urlBuilder
     * @param Translate $translator
     * @param Cache $cache
     * @param DesignInterface $design
     * @param AbstractSession $session
     * @param StoreConfig $storeConfig
     * @param Front $frontController
     * @param FactoryHelper $helperFactory
     * @param ViewUrl $viewUrl
     * @param ViewConfig $viewConfig
     * @param CacheState $cacheState
     * @param Logger $logger
     * @param App $app
     * @param AppState $appState
     * @todo reduce parameter number
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Request $request,
        EventManager $eventManager,
        UrlInterface $urlBuilder,
        Translate $translator,
        Cache $cache,
        DesignInterface $design,
        AbstractSession $session,
        StoreConfig $storeConfig,
        Front $frontController,
        FactoryHelper $helperFactory,
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
        $this->helperFactory   = $helperFactory;
        $this->viewUrl         = $viewUrl;
        $this->viewConfig      = $viewConfig;
        $this->cacheState      = $cacheState;
        $this->logger          = $logger;
        $this->app             = $app;
        $this->appState        = $appState;
    }

    /**
     * @return \Magento\Core\Model\CacheInterface
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @return \Magento\Core\Model\View\Design
     */
    public function getDesignPackage()
    {
        return $this->design;
    }

    /**
     * @return \Magento\Core\Model\Event\Manager
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

    /**
     * @return \Magento\Core\Controller\Varien\Front
     */
    public function getFrontController()
    {
        return $this->frontController;
    }

    /**
     * @return \Magento\Core\Model\Factory\Helper
     */
    public function getHelperFactory()
    {
        return $this->helperFactory;
    }

    /**
     * @return \Magento\View\LayoutInterface
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @return \Magento\Core\Controller\Request\Http
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return \Magento\Core\Model\Session|\Magento\Core\Model\Session\AbstractSession
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
     * @return \Magento\Core\Model\Translate
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @return \Magento\Core\Model\UrlInterface
     */
    public function getUrlBuilder()
    {
        return $this->urlBuilder;
    }

    /**
     * @return \Magento\Core\Model\View\Url
     */
    public function getViewUrl()
    {
        return $this->viewUrl;
    }

    /**
     * @return \Magento\Core\Model\View\Config
     */
    public function getViewConfig()
    {
        return $this->viewConfig;
    }

    /**
     * @return \Magento\Core\Model\Cache\StateInterface
     */
    public function getCacheState()
    {
        return $this->cacheState;
    }

    /**
     * @return \Magento\Core\Model\Logger
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
     * @return \Magento\Core\Model\App\State
     */
    public function getAppState()
    {
        return $this->appState;
    }

    /**
     * Retrieve design theme instance
     *
     * @return Design\Theme
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
     * @param Design\Theme $theme
     * @return Design\Theme
     * @throws \Exception
     */
    protected function getPhysicalTheme(Design\Theme $theme)
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
