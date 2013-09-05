<?php
/**
 * Magento block context object. Contains all block dependencies. Should not be used by any other class
 *
 * {licence_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Core_Block_Template_Context extends Magento_Core_Block_Context
{
    /**
     * Dirs instance
     *
     * @var Magento_Core_Model_Dir
     */
    protected $_dirs;

    /**
     * Logger instance
     *
     * @var Magento_Core_Model_Logger
     */
    protected $_logger;

    /**
     * Filesystem instance
     *
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    /**
     * @var Magento_Core_Model_View_FileSystem
     */
    protected $_viewFileSystem;

    /**
     * @var Magento_Core_Model_TemplateEngine_Factory
     */
    protected $_engineFactory;

    /**
     * @param Magento_Core_Controller_Request_Http $request
     * @param Magento_Core_Model_Layout $layout
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_UrlInterface $urlBuilder
     * @param Magento_Core_Model_Translate $translator
     * @param Magento_Core_Model_CacheInterface $cache
     * @param Magento_Core_Model_View_DesignInterface $design
     * @param Magento_Core_Model_Session $session
     * @param Magento_Core_Model_Store_Config $storeConfig
     * @param Magento_Core_Controller_Varien_Front $frontController
     * @param Magento_Core_Model_Factory_Helper $helperFactory
     * @param Magento_Core_Model_View_Url $viewUrl
     * @param Magento_Core_Model_View_Config $viewConfig
     * @param Magento_Core_Model_Cache_StateInterface $cacheState
     * @param Magento_Core_Model_Dir $dirs
     * @param Magento_Core_Model_Logger $logger
     * @param \Magento\Filesystem $filesystem
     * @param Magento_Core_Model_View_FileSystem $viewFileSystem
     * @param Magento_Core_Model_TemplateEngine_Factory $engineFactory
     */
    public function __construct(
        Magento_Core_Controller_Request_Http $request,
        Magento_Core_Model_Layout $layout,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_UrlInterface $urlBuilder,
        Magento_Core_Model_Translate $translator,
        Magento_Core_Model_CacheInterface $cache,
        Magento_Core_Model_View_DesignInterface $design,
        Magento_Core_Model_Session $session,
        Magento_Core_Model_Store_Config $storeConfig,
        Magento_Core_Controller_Varien_Front $frontController,
        Magento_Core_Model_Factory_Helper $helperFactory,
        Magento_Core_Model_View_Url $viewUrl,
        Magento_Core_Model_View_Config $viewConfig,
        Magento_Core_Model_Cache_StateInterface $cacheState,
        Magento_Core_Model_Dir $dirs,
        Magento_Core_Model_Logger $logger,
        \Magento\Filesystem $filesystem,
        Magento_Core_Model_View_FileSystem $viewFileSystem,
        Magento_Core_Model_TemplateEngine_Factory $engineFactory
    ) {
        parent::__construct(
            $request, $layout, $eventManager, $urlBuilder, $translator, $cache,
            $design, $session, $storeConfig, $frontController, $helperFactory, $viewUrl, $viewConfig, $cacheState
        );

        $this->_dirs = $dirs;
        $this->_logger = $logger;
        $this->_filesystem = $filesystem;
        $this->_viewFileSystem = $viewFileSystem;
        $this->_engineFactory = $engineFactory;
    }

    /**
     * Get dirs instance
     * @return Magento_Core_Model_Dir
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
     * @return Magento_Core_Model_Logger
     */
    public function getLogger()
    {
        return $this->_logger;
    }

    /**
     * Get view file system model
     *
     * @return Magento_Core_Model_View_FileSystem
     */
    public function getViewFileSystem()
    {
        return $this->_viewFileSystem;
    }

    /**
     * Get the template engine factory instance
     *
     * @return Magento_Core_Model_TemplateEngine_Factory
     */
    public function getEngineFactory()
    {
        return $this->_engineFactory;
    }
}
