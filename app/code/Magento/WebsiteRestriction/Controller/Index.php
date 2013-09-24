<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_WebsiteRestriction
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Website stub controller
 *
 * @category    Magento
 * @package     Magento_WebsiteRestriction
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_WebsiteRestriction_Controller_Index extends Magento_Core_Controller_Front_Action
{
    protected $_stubPageIdentifier = Magento_WebsiteRestriction_Model_Config::XML_PATH_RESTRICTION_LANDING_PAGE;

    /**
     * @var Magento_Core_Model_Cache_Type_Config
     */
    protected $_configCacheType;

    protected $_cacheKey;

    /**
     * Prefix for cache id
     */
    protected $_cacheKeyPrefix = 'RESTRICTION_LANGING_PAGE_';

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry;

    /**
     * @var Magento_Core_Model_Website
     */
    protected $_website;

    /**
     * @var Magento_Cms_Model_PageFactory
     */
    protected $_pageFactory;

    /**
     * @var Magento_Core_Model_Store_Config
     */
    protected $_storeConfig;

    /**
     * @var Magento_Core_Model_Locale
     */
    protected $_locale;

    /**
     * @param Magento_Core_Controller_Varien_Action_Context $context
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Core_Model_Cache_Type_Config $configCacheType
     * @param Magento_Core_Model_Website $website
     * @param Magento_Cms_Model_PageFactory $pageFactory
     * @param Magento_Core_Model_Store_Config $storeConfig
     * @param Magento_Core_Model_Locale $locale
     */
    public function __construct(
        Magento_Core_Controller_Varien_Action_Context $context,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Core_Model_Cache_Type_Config $configCacheType,
        Magento_Core_Model_Website $website,
        Magento_Cms_Model_PageFactory $pageFactory,
        Magento_Core_Model_Store_Config $storeConfig,
        Magento_Core_Model_Locale $locale
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_configCacheType = $configCacheType;
        $this->_website = $website;
        $this->_pageFactory = $pageFactory;
        $this->_storeConfig = $storeConfig;
        $this->_locale = $locale;
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_cacheKey = $this->_cacheKeyPrefix . $this->_website->getId();
    }

    /**
     * Display a pre-cached CMS-page if we have such or generate new one
     *
     */
    public function stubAction()
    {
        $cachedData = $this->_configCacheType->load($this->_cacheKey);
        if ($cachedData) {
            $this->getResponse()->setBody($cachedData);
        } else {
            /**
             * Generating page and save it to cache
             */
            /** @var Magento_Cms_Model_Page $page */
            $page = $this->_pageFactory->create()->load(
                $this->_storeConfig->getConfig($this->_stubPageIdentifier),
                'identifier'
            );

            $this->_coreRegistry->register('restriction_landing_page', $page);

            if ($page->getCustomTheme()) {
                if (
                    $this->_locale->isStoreDateInInterval(null, $page->getCustomThemeFrom(), $page->getCustomThemeTo())
                ) {
                    $this->_objectManager->get('Magento_Core_Model_View_DesignInterface')
                        ->setDesignTheme($page->getCustomTheme());
                }
            }

            $this->addActionLayoutHandles();

            if ($page->getRootTemplate()) {
                $this->_objectManager->get('Magento_Page_Helper_Layout')
                    ->applyHandle($page->getRootTemplate());
            }

            $this->loadLayoutUpdates();

            $this->getLayout()->getUpdate()->addUpdate($page->getLayoutUpdateXml());
            $this->generateLayoutXml()->generateLayoutBlocks();

            if ($page->getRootTemplate()) {
                $this->_objectManager->get('Magento_Page_Helper_Layout')
                    ->applyTemplate($page->getRootTemplate());
            }

            $this->renderLayout();

            $this->_configCacheType->save(
                $this->getResponse()->getBody(), $this->_cacheKey, array(Magento_Core_Model_Website::CACHE_TAG)
            );
        }
    }
}
