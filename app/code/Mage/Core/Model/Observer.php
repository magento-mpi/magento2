<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Core Observer model
 */
class Mage_Core_Model_Observer
{
    /**
     * @var Mage_Core_Model_Cache_Frontend_Pool
     */
    private $_cacheFrontendPool;

    /**
     * @var Mage_Core_Model_Theme
     */
    private $_currentTheme;

    /**
     * @var Mage_Core_Model_Page_Asset_Collection
     */
    private $_pageAssets;

    /**
     * Instance of config model
     *
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * @param Mage_Core_Model_Cache_Frontend_Pool $cacheFrontendPool
     * @param Mage_Core_Model_View_DesignInterface $design
     * @param Mage_Core_Model_Page $page
     * @param Mage_Core_Model_ConfigInterface $config
     */
    public function __construct(
        Mage_Core_Model_Cache_Frontend_Pool $cacheFrontendPool,
        Mage_Core_Model_View_DesignInterface $design,
        Mage_Core_Model_Page $page,
        Mage_Core_Model_ConfigInterface $config
    ) {
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->_currentTheme = $design->getDesignTheme();
        $this->_pageAssets = $page->getAssets();
        $this->_config = $config;
    }

    /**
     * Cron job method to clean old cache resources
     *
     * @param Mage_Cron_Model_Schedule $schedule
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function cleanCache(Mage_Cron_Model_Schedule $schedule)
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
     * @param Varien_Event_Observer $observer
     * @return Mage_Core_Model_Observer
     */
    public function themeRegistration(Varien_Event_Observer $observer)
    {
        $baseDir = $observer->getEvent()->getBaseDir();
        $pathPattern = $observer->getEvent()->getPathPattern();
        try {
            Mage::getObjectManager()->get('Mage_Core_Model_Theme_Registration')->register($baseDir, $pathPattern);
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
        }
        return $this;
    }

    /**
     * Apply customized static files to frontend
     *
     * @param Varien_Event_Observer $observer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function applyThemeCustomization(Varien_Event_Observer $observer)
    {
        /** @var $themeFile Mage_Core_Model_Theme_File */
        foreach ($this->_currentTheme->getFiles() as $themeFile) {
            $asset = $themeFile->getAsset();
            if ($asset) {
                $this->_pageAssets->add($themeFile->getFilePath(), $asset);
            }
        }
    }

    /**
     * Rebuild whole config and save to fast storage
     *
     * @param  Varien_Event_Observer $observer
     * @return Mage_Core_Model_Observer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function processReinitConfig(Varien_Event_Observer $observer)
    {
        $this->_config->reinit();
        return $this;
    }
}
