<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_WebsiteRestriction
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\WebsiteRestriction\Controller;

/**
 * Website stub controller
 *
 * @category    Magento
 * @package     Magento_WebsiteRestriction
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Index extends \Magento\App\Action\Action
{
    /**
     * @var string
     */
    protected $_stubPageIdentifier = \Magento\WebsiteRestriction\Model\Config::XML_PATH_RESTRICTION_LANDING_PAGE;

    /**
     * @var \Magento\App\Cache\Type\Config
     */
    protected $_configCacheType;

    /**
     * @var string
     */
    protected $_cacheKey;

    /**
     * Prefix for cache id
     *
     * @var string
     */
    protected $_cacheKeyPrefix = 'RESTRICTION_LANGING_PAGE_';

    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Store\Model\Website
     */
    protected $_website;

    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $_pageFactory;

    /**
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_storeConfig;

    /**
     * @var \Magento\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Registry $coreRegistry
     * @param \Magento\App\Cache\Type\Config $configCacheType
     * @param \Magento\Store\Model\Website $website
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param \Magento\App\Config\ScopeConfigInterface $storeConfig
     * @param \Magento\Stdlib\DateTime\TimezoneInterface $localeDate
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Registry $coreRegistry,
        \Magento\App\Cache\Type\Config $configCacheType,
        \Magento\Store\Model\Website $website,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\App\Config\ScopeConfigInterface $storeConfig,
        \Magento\Stdlib\DateTime\TimezoneInterface $localeDate
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_configCacheType = $configCacheType;
        $this->_website = $website;
        $this->_pageFactory = $pageFactory;
        $this->_storeConfig = $storeConfig;
        $this->_localeDate = $localeDate;
        parent::__construct($context);
        $this->_cacheKey = $this->_cacheKeyPrefix . $this->_website->getId();
    }

    /**
     * Display a pre-cached CMS-page if we have such or generate new one
     *
     * @return void
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
                $this->_storeConfig->getValue(
                    $this->_stubPageIdentifier,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                ),
                'identifier'
            );

            $this->_coreRegistry->register('restriction_landing_page', $page);

            if ($page->getCustomTheme()) {
                if ($this->_localeDate->isScopeDateInInterval(
                    null,
                    $page->getCustomThemeFrom(),
                    $page->getCustomThemeTo()
                )
                ) {
                    $this->_objectManager->get(
                        'Magento\View\DesignInterface'
                    )->setDesignTheme(
                        $page->getCustomTheme()
                    );
                }
            }

            $this->_view->addActionLayoutHandles();

            if ($page->getRootTemplate()) {
                $this->_objectManager->get('Magento\Theme\Helper\Layout')->applyHandle($page->getRootTemplate());
            }

            $this->_view->loadLayoutUpdates();

            $this->_view->getLayout()->getUpdate()->addUpdate($page->getLayoutUpdateXml());
            $this->_view->generateLayoutXml();
            $this->_view->generateLayoutBlocks();

            if ($page->getRootTemplate()) {
                $this->_objectManager->get('Magento\Theme\Helper\Layout')->applyTemplate($page->getRootTemplate());
            }

            $this->_view->renderLayout();

            $this->_configCacheType->save(
                $this->getResponse()->getBody(),
                $this->_cacheKey,
                array(\Magento\Store\Model\Website::CACHE_TAG)
            );
        }
    }
}
