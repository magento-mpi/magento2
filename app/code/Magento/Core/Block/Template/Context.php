<?php
/**
 * Magento block context object. Contains all block dependencies. Should not be used by any other class
 *
 * {licence_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Core\Block\Template;

class Context extends \Magento\Core\Block\Context
{
    /**
     * Dirs instance
     *
     * @var \Magento\App\Dir
     */
    protected $_dirs;

    /**
     * Logger instance
     *
     * @var \Magento\Core\Model\Logger
     */
    protected $_logger;

    /**
     * Filesystem instance
     *
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Core\Model\View\FileSystem
     */
    protected $_viewFileSystem;

    /**
     * @var \Magento\Core\Model\TemplateEngine\Factory
     */
    protected $_engineFactory;

    /**
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\Core\Model\Layout $layout
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Core\Model\UrlInterface $urlBuilder
     * @param \Magento\Core\Model\Translate $translator
     * @param \Magento\Core\Model\CacheInterface $cache
     * @param \Magento\Core\Model\View\DesignInterface $design
     * @param \Magento\Core\Model\Session $session
     * @param \Magento\Core\Model\Store\Config $storeConfig
     * @param \Magento\App\FrontController $frontController
     * @param \Magento\Core\Model\Factory\Helper $helperFactory
     * @param \Magento\Core\Model\View\Url $viewUrl
     * @param \Magento\Core\Model\View\Config $viewConfig
     * @param \Magento\Core\Model\Cache\StateInterface $cacheState
     * @param \Magento\Core\Model\App $app
     * @param \Magento\App\Dir $dirs
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Core\Model\View\FileSystem $viewFileSystem
     * @param \Magento\Core\Model\TemplateEngine\Factory $engineFactory
     */
    public function __construct(
        \Magento\App\RequestInterface $request,
        \Magento\Core\Model\Layout $layout,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Core\Model\UrlInterface $urlBuilder,
        \Magento\Core\Model\Translate $translator,
        \Magento\Core\Model\CacheInterface $cache,
        \Magento\Core\Model\View\DesignInterface $design,
        \Magento\Core\Model\Session $session,
        \Magento\Core\Model\Store\Config $storeConfig,
        \Magento\App\FrontController $frontController,
        \Magento\Core\Model\Factory\Helper $helperFactory,
        \Magento\Core\Model\View\Url $viewUrl,
        \Magento\Core\Model\View\Config $viewConfig,
        \Magento\Core\Model\Cache\StateInterface $cacheState,
        \Magento\App\Dir $dirs,
        \Magento\Core\Model\Logger $logger,
        \Magento\Filesystem $filesystem,
        \Magento\Core\Model\View\FileSystem $viewFileSystem,
        \Magento\Core\Model\TemplateEngine\Factory $engineFactory,
        \Magento\Core\Model\App $app
    ) {
        parent::__construct(
            $request, $layout, $eventManager, $urlBuilder, $translator, $cache, $design, $session,
            $storeConfig, $frontController, $helperFactory, $viewUrl, $viewConfig, $cacheState, $logger, $app
        );

        $this->_dirs = $dirs;
        $this->_logger = $logger;
        $this->_filesystem = $filesystem;
        $this->_viewFileSystem = $viewFileSystem;
        $this->_engineFactory = $engineFactory;
    }

    /**
     * Get dirs instance
     * @return \Magento\App\Dir
     */
    public function getDirs()
    {
        return $this->_dirs;
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
     * @return \Magento\Core\Model\Logger
     */
    public function getLogger()
    {
        return $this->_logger;
    }

    /**
     * Get view file system model
     *
     * @return \Magento\Core\Model\View\FileSystem
     */
    public function getViewFileSystem()
    {
        return $this->_viewFileSystem;
    }

    /**
     * Get the template engine factory instance
     *
     * @return \Magento\Core\Model\TemplateEngine\Factory
     */
    public function getEngineFactory()
    {
        return $this->_engineFactory;
    }
}
