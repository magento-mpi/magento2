<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Framework\View\Element;

/**
 * Abstract block context object
 *
 * Will be used as block constructor modification point after release.
 * Important: Should not be modified by extension developers.
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Context implements \Magento\ObjectManager\ContextInterface
{
    /**
     * Request
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * Layout
     *
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    /**
     * Event manager
     *
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * URL builder
     *
     * @var \Magento\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * Translator
     *
     * @var \Magento\TranslateInterface
     */
    protected $_translator;

    /**
     * Cache
     *
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $_cache;

    /**
     * Design
     *
     * @var \Magento\Framework\View\DesignInterface
     */
    protected $_design;

    /**
     * Session
     *
     * @var \Magento\Session\SessionManagerInterface
     */
    protected $_session;

    /**
     * SID Resolver
     *
     * @var \Magento\Session\SidResolverInterface
     */
    protected $_sidResolver;

    /**
     * Scope config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $_assetRepo;

    /**
     * View config model
     *
     * @var \Magento\Framework\View\ConfigInterface
     */
    protected $_viewConfig;

    /**
     * Cache state
     *
     * @var \Magento\Framework\App\Cache\StateInterface
     */
    protected $_cacheState;

    /**
     * Logger
     *
     * @var \Magento\Logger
     */
    protected $_logger;

    /**
     * Escaper
     *
     * @var \Magento\Escaper
     */
    protected $_escaper;

    /**
     * Filter manager
     *
     * @var \Magento\Filter\FilterManager
     */
    protected $_filterManager;

    /**
     * @var \Magento\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var \Magento\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\UrlInterface $urlBuilder
     * @param \Magento\TranslateInterface $translator
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Magento\Framework\View\DesignInterface $design
     * @param \Magento\Session\SessionManagerInterface $session
     * @param \Magento\Session\SidResolverInterface $sidResolver
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Framework\View\ConfigInterface $viewConfig
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     * @param \Magento\Logger $logger
     * @param \Magento\Escaper $escaper
     * @param \Magento\Filter\FilterManager $filterManager
     * @param \Magento\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Translate\Inline\StateInterface $inlineTranslation
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\UrlInterface $urlBuilder,
        \Magento\TranslateInterface $translator,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Framework\View\DesignInterface $design,
        \Magento\Session\SessionManagerInterface $session,
        \Magento\Session\SidResolverInterface $sidResolver,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\View\ConfigInterface $viewConfig,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Magento\Logger $logger,
        \Magento\Escaper $escaper,
        \Magento\Filter\FilterManager $filterManager,
        \Magento\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Translate\Inline\StateInterface $inlineTranslation
    ) {
        $this->_request = $request;
        $this->_layout = $layout;
        $this->_eventManager = $eventManager;
        $this->_urlBuilder = $urlBuilder;
        $this->_translator = $translator;
        $this->_cache = $cache;
        $this->_design = $design;
        $this->_session = $session;
        $this->_sidResolver = $sidResolver;
        $this->_scopeConfig = $scopeConfig;
        $this->_assetRepo = $assetRepo;
        $this->_viewConfig = $viewConfig;
        $this->_cacheState = $cacheState;
        $this->_logger = $logger;
        $this->_escaper = $escaper;
        $this->_filterManager = $filterManager;
        $this->_localeDate = $localeDate;
        $this->inlineTranslation = $inlineTranslation;
    }

    /**
     * Get cache
     *
     * @return \Magento\Framework\App\CacheInterface
     */
    public function getCache()
    {
        return $this->_cache;
    }

    /**
     * Get design package
     *
     * @return \Magento\Framework\View\DesignInterface
     */
    public function getDesignPackage()
    {
        return $this->_design;
    }

    /**
     * Get event manager
     *
     * @return \Magento\Event\ManagerInterface
     */
    public function getEventManager()
    {
        return $this->_eventManager;
    }

    /**
     * Get layout
     *
     * @return \Magento\Framework\View\LayoutInterface
     */
    public function getLayout()
    {
        return $this->_layout;
    }

    /**
     * Get request
     *
     * @return \Magento\Framework\App\RequestInterface
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Get session
     *
     * @return \Magento\Session\SessionManagerInterface
     */
    public function getSession()
    {
        return $this->_session;
    }

    /**
     * Get SID resolver
     *
     * @return \Magento\Session\SidResolverInterface
     */
    public function getSidResolver()
    {
        return $this->_sidResolver;
    }

    /**
     * Get scope config
     *
     * @return \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public function getScopeConfig()
    {
        return $this->_scopeConfig;
    }

    /**
     * Get translator
     *
     * @return \Magento\TranslateInterface
     */
    public function getTranslator()
    {
        return $this->_translator;
    }

    /**
     * Get inline translation status object
     *
     * @return \Magento\Translate\Inline\StateInterface
     */
    public function getInlineTranslation()
    {
        return $this->inlineTranslation;
    }

    /**
     * Get URL builder
     *
     * @return \Magento\UrlInterface
     */
    public function getUrlBuilder()
    {
        return $this->_urlBuilder;
    }

    /**
     * Get asset service
     *
     * @return \Magento\Framework\View\Asset\Repository
     */
    public function getAssetRepository()
    {
        return $this->_assetRepo;
    }

    /**
     * Get view config
     *
     * @return \Magento\Framework\View\ConfigInterface
     */
    public function getViewConfig()
    {
        return $this->_viewConfig;
    }

    /**
     * Get cache state
     *
     * @return \Magento\Framework\App\Cache\StateInterface
     */
    public function getCacheState()
    {
        return $this->_cacheState;
    }

    /**
     * Get logger
     *
     * @return \Magento\Logger
     */
    public function getLogger()
    {
        return $this->_logger;
    }

    /**
     * Get escaper
     *
     * @return \Magento\Escaper
     */
    public function getEscaper()
    {
        return $this->_escaper;
    }

    /**
     * Get filter manager
     *
     * @return \Magento\Filter\FilterManager
     */
    public function getFilterManager()
    {
        return $this->_filterManager;
    }

    /**
     * @return \Magento\Stdlib\DateTime\TimezoneInterface
     */
    public function getLocaleDate()
    {
        return $this->_localeDate;
    }
}
