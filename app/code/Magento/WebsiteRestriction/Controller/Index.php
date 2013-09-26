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
namespace Magento\WebsiteRestriction\Controller;

class Index extends \Magento\Core\Controller\Front\Action
{
    protected $_stubPageIdentifier = \Magento\WebsiteRestriction\Model\Config::XML_PATH_RESTRICTION_LANDING_PAGE;

    /**
     * @var \Magento\Core\Model\Cache\Type\Config
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
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Core\Model\Website
     */
    protected $_website;

    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $_pageFactory;

    /**
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_storeConfig;

    /**
     * @var \Magento\Core\Model\Locale
     */
    protected $_locale;

    /**
     * @param \Magento\Core\Controller\Varien\Action\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Core\Model\Cache\Type\Config $configCacheType
     * @param \Magento\Core\Model\Website $website
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param \Magento\Core\Model\Store\Config $storeConfig
     * @param \Magento\Core\Model\Locale $locale
     */
    public function __construct(
        \Magento\Core\Controller\Varien\Action\Context $context,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Core\Model\Cache\Type\Config $configCacheType,
        \Magento\Core\Model\Website $website,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Core\Model\Store\Config $storeConfig,
        \Magento\Core\Model\Locale $locale
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
            /** @var \Magento\Cms\Model\Page $page */
            $page = $this->_pageFactory->create()->load(
                $this->_storeConfig->getConfig($this->_stubPageIdentifier),
                'identifier'
            );

            $this->_coreRegistry->register('restriction_landing_page', $page);

            if ($page->getCustomTheme()) {
                if (
                    $this->_locale->isStoreDateInInterval(null, $page->getCustomThemeFrom(), $page->getCustomThemeTo())
                ) {
                    $this->_objectManager->get('Magento\Core\Model\View\DesignInterface')
                        ->setDesignTheme($page->getCustomTheme());
                }
            }

            $this->addActionLayoutHandles();

            if ($page->getRootTemplate()) {
                $this->_objectManager->get('Magento\Page\Helper\Layout')
                    ->applyHandle($page->getRootTemplate());
            }

            $this->loadLayoutUpdates();

            $this->getLayout()->getUpdate()->addUpdate($page->getLayoutUpdateXml());
            $this->generateLayoutXml()->generateLayoutBlocks();

            if ($page->getRootTemplate()) {
                $this->_objectManager->get('Magento\Page\Helper\Layout')
                    ->applyTemplate($page->getRootTemplate());
            }

            $this->renderLayout();

            $this->_configCacheType->save(
                $this->getResponse()->getBody(), $this->_cacheKey, array(\Magento\Core\Model\Website::CACHE_TAG)
            );
        }
    }
}
