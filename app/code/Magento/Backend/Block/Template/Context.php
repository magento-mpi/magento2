<?php
/**
 * Backend block template context
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Magento_Backend_Block_Template_Context extends Magento_Core_Block_Template_Context
{
    /**
     * @var Magento_AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_LocaleInterface $locale
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
     * @param Magento_Core_Model_App $app
     * @param Magento_Core_Model_Dir $dirs
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Filesystem $filesystem
     * @param Magento_Core_Model_View_FileSystem $viewFileSystem
     * @param Magento_Core_Model_TemplateEngine_Factory $engineFactory
     * @param Magento_AuthorizationInterface $authorization
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_LocaleInterface $locale,
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
        Magento_Filesystem $filesystem,
        Magento_Core_Model_View_FileSystem $viewFileSystem,
        Magento_Core_Model_TemplateEngine_Factory $engineFactory,
        Magento_AuthorizationInterface $authorization,
        Magento_Core_Model_App $app
    ) {
        parent::__construct(
            $request, $layout, $eventManager, $urlBuilder, $translator, $cache, $design, $session, $storeConfig,
            $frontController, $helperFactory, $viewUrl, $viewConfig, $cacheState,
            $dirs, $logger, $filesystem, $viewFileSystem, $engineFactory, $app
        );
        $this->_storeManager = $storeManager;
        $this->_locale = $locale;
        $this->_authorization = $authorization;
    }

    /**
     * Get store manager
     *
     * @return Magento_Core_Model_StoreManager
     */
    public function getStoreManager()
    {
        return $this->_storeManager;
    }

    /**
     * Get locale
     * @return Magento_Core_Model_LocaleInterface
     */
    public function getLocale()
    {
        return $this->_locale;
    }

    /**
     * Retrieve Authorization
     *
     * @return \Magento_AuthorizationInterface
     */
    public function getAuthorization()
    {
        return $this->_authorization;
    }
}
