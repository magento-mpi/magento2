<?php
/**
 * {licence_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\View\Element\Template;

/**
 * Magento block context object
 *
 * Contains all block dependencies. Should not be used by any other class
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Context extends \Magento\View\Element\Context
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
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\View\FileSystem
     */
    protected $_viewFileSystem;

    /**
     * @var \Magento\View\TemplateEnginePool
     */
    protected $enginePool;

    /**
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\View\LayoutInterface $layout
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\UrlInterface $urlBuilder
     * @param \Magento\TranslateInterface $translator
     * @param \Magento\App\CacheInterface $cache
     * @param \Magento\View\DesignInterface $design
     * @param \Magento\Session\SessionManagerInterface $session
     * @param \Magento\Session\SidResolverInterface $sidResolver
     * @param \Magento\Core\Model\Store\Config $storeConfig
     * @param \Magento\App\FrontController $frontController
     * @param \Magento\View\Url $viewUrl
     * @param \Magento\View\ConfigInterface $viewConfig
     * @param \Magento\App\Cache\StateInterface $cacheState
     * @param \Magento\Logger $logger
     * @param \Magento\Core\Model\App $app
     * @param \Magento\Escaper $escaper
     * @param \Magento\Filter\FilterManager $filterManager
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\View\FileSystem $viewFileSystem
     * @param \Magento\View\TemplateEnginePool $enginePool
     * @param \Magento\App\State $appState
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param array $data
     * 
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\App\RequestInterface $request,
        \Magento\View\LayoutInterface $layout,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\UrlInterface $urlBuilder,
        \Magento\TranslateInterface $translator,
        \Magento\App\CacheInterface $cache,
        \Magento\View\DesignInterface $design,
        \Magento\Session\SessionManagerInterface $session,
        \Magento\Session\SidResolverInterface $sidResolver,
        \Magento\Core\Model\Store\Config $storeConfig,
        \Magento\App\FrontController $frontController,
        \Magento\View\Url $viewUrl,
        \Magento\View\ConfigInterface $viewConfig,
        \Magento\App\Cache\StateInterface $cacheState,
        \Magento\Logger $logger,
        \Magento\Core\Model\App $app,
        \Magento\Escaper $escaper,
        \Magento\Filter\FilterManager $filterManager,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Filesystem $filesystem,
        \Magento\View\FileSystem $viewFileSystem,
        \Magento\View\TemplateEnginePool $enginePool,
        \Magento\App\State $appState,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        array $data = array()
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
            $storeConfig,
            $frontController,
            $viewUrl,
            $viewConfig,
            $cacheState,
            $logger,
            $app,
            $escaper,
            $filterManager,
            $locale,
            $data
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
     * @return \Magento\Filesystem
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
     * @return \Magento\View\FileSystem
     */
    public function getViewFileSystem()
    {
        return $this->_viewFileSystem;
    }

    /**
     * Get the template engine pool instance
     *
     * @return \Magento\View\TemplateEnginePool
     */
    public function getEnginePool()
    {
        return $this->enginePool;
    }

    /**
     * Get app state object
     *
     * @return \Magento\App\State
     */
    public function getAppState()
    {
        return $this->_appState;
    }

    /**
     * Get store manager
     *
     * @return \Magento\Core\Model\StoreManagerInterface
     */
    public function getStoreManager()
    {
        return $this->_storeManager;
    }
}
