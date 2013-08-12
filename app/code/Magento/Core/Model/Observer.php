<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Core Observer model
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Magento_Core_Model_Observer
{
    /**
     * @var Magento_Core_Model_Cache_Frontend_Pool
     */
    private $_cacheFrontendPool;

    /**
     * @var Magento_Core_Model_Theme
     */
    private $_currentTheme;

    /**
     * @var Magento_Core_Model_Page_Asset_Collection
     */
    private $_pageAssets;

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_config;

    /**
     * @var Magento_Core_Model_Page_Asset_PublicFileFactory
     */
    protected $_assetFileFactory;

    /**
     * @var Magento_Core_Model_Logger
     */
    protected $_logger;

    /**
     * @param Magento_Core_Model_Cache_Frontend_Pool $cacheFrontendPool
     * @param Magento_Core_Model_View_DesignInterface $design
     * @param Magento_Core_Model_Page $page
     * @param Magento_Core_Model_ConfigInterface $config
     * @param Magento_Core_Model_Page_Asset_PublicFileFactory $assetFileFactory
     * @param Magento_Core_Model_Logger $logger
     */
    public function __construct(
        Magento_Core_Model_Cache_Frontend_Pool $cacheFrontendPool,
        Magento_Core_Model_View_DesignInterface $design,
        Magento_Core_Model_Page $page,
        Magento_Core_Model_ConfigInterface $config,
        Magento_Core_Model_Page_Asset_PublicFileFactory $assetFileFactory,
        Magento_Core_Model_Logger $logger
    ) {
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->_currentTheme = $design->getDesignTheme();
        $this->_pageAssets = $page->getAssets();
        $this->_config = $config;
        $this->_assetFileFactory = $assetFileFactory;
        $this->_logger = $logger;
    }

    /**
     * Cron job method to clean old cache resources
     *
     * @param Magento_Cron_Model_Schedule $schedule
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function cleanCache(Magento_Cron_Model_Schedule $schedule)
    {
        /** @var $cacheFrontend Magento_Cache_FrontendInterface */
        foreach ($this->_cacheFrontendPool as $cacheFrontend) {
            // Magento cache frontend does not support the 'old' cleaning mode, that's why backend is used directly
            $cacheFrontend->getBackend()->clean(Zend_Cache::CLEANING_MODE_OLD);
        }
    }

    /**
     * Theme registration
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Core_Model_Observer
     */
    public function themeRegistration(Magento_Event_Observer $observer)
    {
        $baseDir = $observer->getEvent()->getBaseDir();
        $pathPattern = $observer->getEvent()->getPathPattern();
        try {
            Mage::getObjectManager()->get('Magento_Core_Model_Theme_Registration')->register($baseDir, $pathPattern);
        } catch (Magento_Core_Exception $e) {
            $this->_logger->logException($e);
        }
        return $this;
    }

    /**
     * Apply customized static files to frontend
     *
     * @param Magento_Event_Observer $observer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function applyThemeCustomization(Magento_Event_Observer $observer)
    {
        /** @var $themeFile Magento_Core_Model_Theme_File */
        foreach ($this->_currentTheme->getCustomization()->getFiles() as $themeFile) {
            try {
                $service = $themeFile->getCustomizationService();
                if ($service instanceof Magento_Core_Model_Theme_Customization_FileAssetInterface) {
                    $asset = $this->_assetFileFactory->create(array(
                        'file'        => $themeFile->getFullPath(),
                        'contentType' => $service->getContentType()
                    ));
                    $this->_pageAssets->add($themeFile->getData('file_path'), $asset);
                }
            } catch (InvalidArgumentException $e) {
                $this->_logger->logException($e);
            }
        }
    }

    /**
     * Rebuild whole config and save to fast storage
     *
     * @param  Magento_Event_Observer $observer
     * @return Magento_Core_Model_Observer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function processReinitConfig(Magento_Event_Observer $observer)
    {
        $this->_config->reinit();
        return $this;
    }
}
