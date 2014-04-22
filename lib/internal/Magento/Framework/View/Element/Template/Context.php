<?php
/**
 * {licence_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Framework\View\Element\Template;

/**
 * Magento block context object
 *
 * Contains all block dependencies. Should not be used by any other class
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Context extends \Magento\Framework\View\Element\Context
{
    /**
     * Logger instance
     *
     * @var \Magento\Logger
     */
    protected $_logger;

    /**
     * Filesystem instance
     *
     * @var \Magento\Framework\App\Filesystem
     */
    protected $_filesystem;

    /**
     * View file system
     *
     * @var \Magento\Framework\View\FileSystem
     */
    protected $_viewFileSystem;

    /**
     * Template engine pool
     *
     * @var \Magento\Framework\View\TemplateEnginePool
     */
    protected $enginePool;

    /**
     * Application state
     *
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

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
     * @param \Magento\Framework\App\Filesystem $filesystem
     * @param \Magento\Framework\View\FileSystem $viewFileSystem
     * @param \Magento\Framework\View\TemplateEnginePool $enginePool
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
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
        \Magento\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Filesystem $filesystem,
        \Magento\Framework\View\FileSystem $viewFileSystem,
        \Magento\Framework\View\TemplateEnginePool $enginePool,
        \Magento\Framework\App\State $appState,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct(
            $request,
            $layout,
            $eventManager,
            $urlBuilder,
            $translator,
            $cache,
            $design,
            $session,
            $sidResolver,
            $scopeConfig,
            $assetRepo,
            $viewConfig,
            $cacheState,
            $logger,
            $escaper,
            $filterManager,
            $localeDate,
            $inlineTranslation
        );

        $this->_storeManager = $storeManager;
        $this->_appState = $appState;
        $this->_logger = $logger;
        $this->_filesystem = $filesystem;
        $this->_viewFileSystem = $viewFileSystem;
        $this->enginePool = $enginePool;
    }

    /**
     * Get filesystem instance
     *
     * @return \Magento\Framework\App\Filesystem
     */
    public function getFilesystem()
    {
        return $this->_filesystem;
    }

    /**
     * Get logger instance
     *
     * @return \Magento\Logger
     */
    public function getLogger()
    {
        return $this->_logger;
    }

    /**
     * Get view file system model
     *
     * @return \Magento\Framework\View\FileSystem
     */
    public function getViewFileSystem()
    {
        return $this->_viewFileSystem;
    }

    /**
     * Get the template engine pool instance
     *
     * @return \Magento\Framework\View\TemplateEnginePool
     */
    public function getEnginePool()
    {
        return $this->enginePool;
    }

    /**
     * Get app state object
     *
     * @return \Magento\Framework\App\State
     */
    public function getAppState()
    {
        return $this->_appState;
    }

    /**
     * Get store manager
     *
     * @return \Magento\Store\Model\StoreManagerInterface
     */
    public function getStoreManager()
    {
        return $this->_storeManager;
    }
}
