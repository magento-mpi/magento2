<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\FullPageCache\Model;

/**
 * Full page cache observer
 */
class Observer
{
    /**
     * Design exception key
     */
    const XML_PATH_DESIGN_EXCEPTION = 'design/package/ua_regexp';

    /**
     * Page Cache Processor
     *
     * @var \Magento\FullPageCache\Model\Processor
     */
    protected $_processor;

    /**
     * Page Cache Config
     *
     * @var \Magento\FullPageCache\Model\Placeholder\Mapper
     */
    protected $_mapper;

    /**
     * Is Enabled Full Page Cache
     *
     * @var bool
     */
    protected $_isEnabled;

    /**
     * @var \Magento\App\Cache\StateInterface
     */
    protected $_cacheState;

    /**
     * @var \Magento\FullPageCache\Model\Cookie
     */
    protected $_cookie;

    /**
     * FPC cache model
     *
     * @var \Magento\FullPageCache\Model\Cache
     */
    protected $_fpcCache;

    /**
     * FPC processor restriction model
     *
     * @var \Magento\FullPageCache\Model\Processor\RestrictionInterface
     */
    protected $_restriction;

    /**
     * Request identifier model
     *
     * @var \Magento\FullPageCache\Model\Request\Identifier
     */
    protected $_requestIdentifier;

    /**
     * Design rules
     *
     * @var \Magento\FullPageCache\Model\DesignPackage\Rules
     */
    protected $_designRules;

    /**
     * Catalog product compare
     *
     * @var \Magento\Catalog\Helper\Product\Compare
     */
    protected $_ctlgProdCompare = null;

    /**
     * Wishlist data
     *
     * @var \Magento\Wishlist\Helper\Data
     */
    protected $_wishlistData = null;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Logger
     */
    protected $_logger;

    /**
     * @var \Magento\App\Cache\TypeListInterface
     */
    protected $_typeList;

    /**
     * @var \Magento\Catalog\Model\Session
     */
    protected $_catalogSession;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_productVisibility;

    /**
     * @var \Magento\FullPageCache\Model\Container\PlaceholderFactory
     */
    protected $_fpcPlacehldrFactory;

    /**
     * @var \Magento\Reports\Model\Resource\Product\Index\Viewed\CollectionFactory
     */
    protected $_reportsFactory;

    /**
     * @var \Magento\FullPageCache\Model\ValidatorFactory
     */
    protected $_fpcValidatorFactory;

    /**
     * @var \Magento\Reports\Model\Product\Index\ViewedFactory
     */
    protected $_viewedIdxFactory;

    /**
     * @var \Magento\FullPageCache\Model\Container\WishlistsFactory
     */
    protected $_fpcWishlistsFactory;

    /**
     * @var \Magento\Session\Config\ConfigInterface
     */
    protected $_sessionConfig;

    /**
     * @param \Magento\Wishlist\Helper\Data $wishlistData
     * @param \Magento\Catalog\Helper\Product\Compare $ctlgProdCompare
     * @param \Magento\FullPageCache\Model\Processor $processor
     * @param \Magento\FullPageCache\Model\Request\Identifier $_requestIdentifier
     * @param \Magento\FullPageCache\Model\Placeholder\Mapper $mapper
     * @param \Magento\App\Cache\StateInterface $cacheState
     * @param \Magento\FullPageCache\Model\Cache $fpcCache
     * @param \Magento\FullPageCache\Model\Cookie $cookie
     * @param \Magento\FullPageCache\Model\Processor\RestrictionInterface $restriction
     * @param \Magento\FullPageCache\Model\DesignPackage\Rules $designRules
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Logger $logger
     * @param \Magento\App\Cache\TypeListInterface $typeList
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\FullPageCache\Model\Container\PlaceholderFactory $fpcPlacehldrFactory
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param \Magento\Catalog\Model\Session $catalogSession
     * @param \Magento\Reports\Model\Resource\Product\Index\Viewed\CollectionFactory $reportsFactory
     * @param \Magento\FullPageCache\Model\ValidatorFactory $fpcValidatorFactory
     * @param \Magento\Reports\Model\Product\Index\ViewedFactory $viewedIdxFactory
     * @param \Magento\FullPageCache\Model\Container\WishlistsFactory $fpcWishlistsFactory
     * @param \Magento\Session\Config\ConfigInterface $sessionConfig
     */
    public function __construct(
        \Magento\Wishlist\Helper\Data $wishlistData,
        \Magento\Catalog\Helper\Product\Compare $ctlgProdCompare,
        \Magento\FullPageCache\Model\Processor $processor,
        \Magento\FullPageCache\Model\Request\Identifier $_requestIdentifier,
        \Magento\FullPageCache\Model\Placeholder\Mapper $mapper,
        \Magento\App\Cache\StateInterface $cacheState,
        \Magento\FullPageCache\Model\Cache $fpcCache,
        \Magento\FullPageCache\Model\Cookie $cookie,
        \Magento\FullPageCache\Model\Processor\RestrictionInterface $restriction,
        \Magento\FullPageCache\Model\DesignPackage\Rules $designRules,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Logger $logger,
        \Magento\App\Cache\TypeListInterface $typeList,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\FullPageCache\Model\Container\PlaceholderFactory $fpcPlacehldrFactory,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Reports\Model\Resource\Product\Index\Viewed\CollectionFactory $reportsFactory,
        \Magento\FullPageCache\Model\ValidatorFactory $fpcValidatorFactory,
        \Magento\Reports\Model\Product\Index\ViewedFactory $viewedIdxFactory,
        \Magento\FullPageCache\Model\Container\WishlistsFactory $fpcWishlistsFactory,
        \Magento\Session\Config\ConfigInterface $sessionConfig
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_wishlistData = $wishlistData;
        $this->_ctlgProdCompare = $ctlgProdCompare;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_processor = $processor;
        $this->_mapper    = $mapper;
        $this->_cacheState = $cacheState;
        $this->_fpcCache = $fpcCache;
        $this->_cookie = $cookie;
        $this->_restriction = $restriction;
        $this->_requestIdentifier = $_requestIdentifier;
        $this->_designRules = $designRules;
        $this->_isEnabled = $this->_cacheState->isEnabled('full_page');
        $this->_logger = $logger;
        $this->_typeList = $typeList;
        $this->_fpcPlacehldrFactory = $fpcPlacehldrFactory;
        $this->_productVisibility = $productVisibility;
        $this->_catalogSession = $catalogSession;
        $this->_reportsFactory = $reportsFactory;
        $this->_fpcValidatorFactory = $fpcValidatorFactory;
        $this->_viewedIdxFactory = $viewedIdxFactory;
        $this->_fpcWishlistsFactory = $fpcWishlistsFactory;
        $this->_sessionConfig = $sessionConfig;
    }

    /**
     * Check if full page cache is enabled
     *
     * @return bool
     */
    public function isCacheEnabled()
    {
        return $this->_isEnabled;
    }

    /**
     * Save page body to cache storage
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function cacheResponse(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $event = $observer->getEvent();
        $request = $event->getRequest();
        $response = $event->getResponse();
        $this->_saveDesignException();
        $this->_processor->processRequestResponse($request, $response);
        return $this;
    }

    /**
     * Check when cache should be disabled
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function processPreDispatch(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $action = $observer->getEvent()->getControllerAction();
        /* @var $request \Magento\App\RequestInterface */
        $request = $action->getRequest();
        /**
         * Check if request will be cached
         */
        if ($this->_processor->canProcessRequest($request) && $this->_processor->getRequestProcessor($request)) {
            $this->_cacheState->setEnabled(\Magento\View\Element\AbstractBlock::CACHE_GROUP, false); // disable blocks cache
            $this->_catalogSession->setParamsMemorizeDisabled(true);
        } else {
            $this->_catalogSession->setParamsMemorizeDisabled(false);
        }
        $this->_cookie->updateCustomerCookies();
        return $this;
    }

    /**
     * Checks whether exists design exception value in cache.
     * If not, gets it from config and puts into cache
     *
     * @return \Magento\FullPageCache\Model\Observer
     */
    protected function _saveDesignException()
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $cacheId = \Magento\FullPageCache\Model\DesignPackage\Info::DESIGN_EXCEPTION_KEY;
        $exception = $this->_fpcCache->load($cacheId);
        if (!$exception) {
            $exception = $this->_coreStoreConfig->getConfig(self::XML_PATH_DESIGN_EXCEPTION);
            $this->_fpcCache->save($exception, $cacheId);
            $this->_requestIdentifier->refreshRequestIds();
        }
        return $this;
    }

    /**
     * model_load_after event processor. Collect tags of all loaded entities
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function registerModelTag(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $object = $observer->getEvent()->getObject();
        if ($object && $object->getId()) {
            $tags = $object->getCacheIdTags();
            if ($tags) {
                $this->_processor->addRequestTag($tags);
            }
        }
        return $this;
    }

    /**
     * Check category state on post dispatch to allow category page be cached
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function checkCategoryState(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $category = $this->_coreRegistry->registry('current_category');
        /**
         * Categories with category event can't be cached
         */
        if ($category && $category->getEvent()) {
            $this->_restriction->setIsDenied();
        }
        return $this;
    }

    /**
     * Check product state on post dispatch to allow product page be cached
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function checkProductState(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $product = $this->_coreRegistry->registry('current_product');
        /**
         * Categories with category event can't be cached
         */
        if ($product && $product->getEvent()) {
            $this->_restriction->setIsDenied();
        }
        return $this;
    }

    /**
     * Check if data changes during object save affect cached pages
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function validateDataChanges(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $this->_fpcValidatorFactory
            ->create()
            ->checkDataChange($observer->getEvent()->getObject());
        return $this;
    }

    /**
     * Check if data delete affect cached pages
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function validateDataDelete(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $this->_fpcValidatorFactory
            ->create()
            ->checkDataDelete($observer->getEvent()->getObject());
        return $this;
    }

    /**
     * Clean full page cache
     *
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function cleanCache()
    {
        $this->_fpcCache->clean(\Magento\FullPageCache\Model\Processor::CACHE_TAG);
        return $this;
    }

    /**
     * Invalidate full page cache
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function invalidateCache()
    {
        $this->_typeList->invalidate('full_page');
        return $this;
    }

    /**
     * Render placeholder tags around the block if needed
     *
     * Event: core_layout_render_element
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function renderBlockPlaceholder(\Magento\Event\Observer $observer)
    {
        if (!$this->_isEnabled) {
            return $this;
        }
        $event = $observer->getEvent();
        /** @var $layout \Magento\View\LayoutInterface */
        $layout = $event->getData('layout');
        $name = $event->getData('element_name');
        if (!($block = $layout->getBlock($name))) {
            return $this;
        }

        $transport = $event->getData('transport');
        $placeholder = $this->_mapper->map($block);
        if ($transport && $placeholder && !$block->getSkipRenderTag()) {
            $blockHtml = $transport->getData('output');
            $blockHtml = $placeholder->getStartTag() . $blockHtml . $placeholder->getEndTag();
            $transport->setData('output', $blockHtml);
        }
        return $this;
    }

    /**
     * Set cart hash in cookie on quote change
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function registerQuoteChange(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        /** @var \Magento\Sales\Model\Quote */
        $quote = ($observer->getEvent()->getQuote()) ? $observer->getEvent()->getQuote() :
            $observer->getEvent()->getQuoteItem()->getQuote();
        $this->_cookie->setObscure(\Magento\FullPageCache\Model\Cookie::COOKIE_CART, 'quote_' . $quote->getId());

        $cacheId = \Magento\FullPageCache\Model\Container\Advanced\Quote::getCacheId();
        $this->_fpcCache->remove($cacheId);

        return $this;
    }

    /**
     * Set compare list in cookie on list change. Also modify recently compared cookie.
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function registerCompareListChange(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }

        $listItems = $this->_ctlgProdCompare->getItemCollection();
        $previousList = $this->_cookie->get(\Magento\FullPageCache\Model\Cookie::COOKIE_COMPARE_LIST);
        $previousList = (empty($previousList)) ? array() : explode(',', $previousList);

        $ids = array();
        foreach ($listItems as $item) {
            $ids[] = $item->getId();
        }
        sort($ids);
        $this->_cookie->set(
            \Magento\FullPageCache\Model\Cookie::COOKIE_COMPARE_LIST,
            implode(',', $ids),
            $this->_catalogSession->getCookieLifetime(),
            $this->_catalogSession->getCookiePath()
        );

        //Recently compared products processing
        $recentlyComparedProducts = $this->_cookie
            ->get(\Magento\FullPageCache\Model\Cookie::COOKIE_RECENTLY_COMPARED);
        $recentlyComparedProducts = (empty($recentlyComparedProducts)) ? array()
            : explode(',', $recentlyComparedProducts);

        //Adding products deleted from compare list to "recently compared products"
        $deletedProducts = array_diff($previousList, $ids);
        $recentlyComparedProducts = array_merge($recentlyComparedProducts, $deletedProducts);

        //Removing products from recently product list if it's present in compare list
        $addedProducts = array_diff($ids, $previousList);
        $recentlyComparedProducts = array_diff($recentlyComparedProducts, $addedProducts);

        $recentlyComparedProducts = array_unique($recentlyComparedProducts);
        sort($recentlyComparedProducts);

        $this->_cookie->set(
            \Magento\FullPageCache\Model\Cookie::COOKIE_RECENTLY_COMPARED,
            implode(',', $recentlyComparedProducts),
            $this->_catalogSession->getCookieLifetime(),
            $this->_catalogSession->getCookiePath()
        );
        return $this;
    }

    /**
     * Set new message cookie on adding message to session
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function processNewMessage(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $this->_cookie->set(
            \Magento\FullPageCache\Model\Cookie::COOKIE_MESSAGE,
            '1',
            $this->_sessionConfig->getCookieLifetime(),
            $this->_sessionConfig->getCookiePath(),
            $this->_sessionConfig->getCookieDomain()
        );
        return $this;
    }


    /**
     * Update customer viewed products index and renew customer viewed product ids cookie
     *
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function updateCustomerProductIndex()
    {
        try {
            $productIds = $this->_cookie->get(\Magento\FullPageCache\Model\Container\Viewedproducts::COOKIE_NAME);
            if ($productIds) {
                $productIds = explode(',', $productIds);
                $this->_viewedIdxFactory->create()->registerIds($productIds);
            }
        } catch (\Exception $e) {
            $this->_logger->logException($e);
        }

        // renew customer viewed product ids cookie
        $countLimit = $this->_coreStoreConfig->getConfig(
            \Magento\Reports\Block\Product\Viewed::XML_PATH_RECENTLY_VIEWED_COUNT
        );
        $collection = $this->_reportsFactory
            ->create()
            ->addIndexFilter()
            ->setAddedAtOrder()
            ->setPageSize($countLimit)
            ->setCurPage(1)
            ->setVisibility($this->_productVisibility->getVisibleInSiteIds());

        $productIds = $collection->load()->getLoadedIds();
        $productIds = implode(',', $productIds);
        $this->_cookie->registerViewedProducts($productIds, $countLimit, false);
        return $this;
    }

    /**
     * Set cookie for logged in customer
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function customerLogin(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $this->_cookie->updateCustomerCookies();
        $this->updateCustomerProductIndex();
        return $this;
    }

    /**
     * Remove customer cookie
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function customerLogout(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $this->_cookie->updateCustomerCookies();

        if (!$this->_cookie->get(\Magento\FullPageCache\Model\Cookie::COOKIE_CUSTOMER)) {
            $this->_cookie->set(
                \Magento\FullPageCache\Model\Cookie::COOKIE_RECENTLY_COMPARED,
                null,
                $this->_catalogSession->getCookieLifetime(),
                $this->_catalogSession->getCookiePath()
            );
            $this->_cookie->set(
                \Magento\FullPageCache\Model\Cookie::COOKIE_COMPARE_LIST,
                null,
                $this->_catalogSession->getCookieLifetime(),
                $this->_catalogSession->getCookiePath()
            );
            \Magento\FullPageCache\Model\Cookie::registerViewedProducts(array(), 0, false);
        }

        return $this;
    }

    /**
     * Set wishlist hash in cookie on wishlist change
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function registerWishlistChange(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }

        $cookieValue = '';
        foreach ($this->_wishlistData->getWishlistItemCollection() as $item) {
            $cookieValue .= ($cookieValue ? '_' : '') . $item->getId();
        }

        // Wishlist sidebar hash
        $this->_cookie->setObscure(\Magento\FullPageCache\Model\Cookie::COOKIE_WISHLIST, $cookieValue);

        // Wishlist items count hash for top link
        $this->_cookie->setObscure(\Magento\FullPageCache\Model\Cookie::COOKIE_WISHLIST_ITEMS,
            'wishlist_item_count_' . $this->_wishlistData->getItemCount());

        return $this;
    }

    /**
     * Clear wishlist list
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function registerWishlistListChange(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $placeholder = $this->_fpcPlacehldrFactory
            ->create('WISHLISTS');

        $blockContainer = $this->_fpcWishlistsFactory->create(array('placeholder' => $placeholder));
        $this->_fpcCache->remove($blockContainer->getCacheId());

        return $this;
    }

    /**
     * Clean order sidebar cache
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function registerNewOrder(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }

        // Customer order sidebar tag
        $cacheId = md5($this->_cookie->get(\Magento\FullPageCache\Model\Cookie::COOKIE_CUSTOMER));
        $this->_fpcCache->remove($cacheId);
        return $this;
    }

    /**
     * Remove new message cookie on clearing session messages.
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function processMessageClearing(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $this->_cookie->set(
            \Magento\FullPageCache\Model\Cookie::COOKIE_MESSAGE,
            null
        );
        return $this;
    }

    /**
     * Re-save exception rules to cache storage
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function registerDesignExceptionsChange(\Magento\Event\Observer $observer)
    {
        $object = $observer->getDataObject();
        $this->_fpcCache->save(
            $object->getValue(),
            \Magento\FullPageCache\Model\DesignPackage\Info::DESIGN_EXCEPTION_KEY,
            array(\Magento\FullPageCache\Model\Processor::CACHE_TAG)
        );
        return $this;
    }

    /**
     * Update info about product on product page
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function updateProductInfo(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }

        $paramsObject = $observer->getEvent()->getParams();
        if ($paramsObject instanceof \Magento\Object) {
            if (array_key_exists(\Magento\FullPageCache\Model\Cookie::COOKIE_CATEGORY_ID, $_COOKIE)) {
                $paramsObject->setCategoryId($_COOKIE[\Magento\FullPageCache\Model\Cookie::COOKIE_CATEGORY_ID]);
            }
        }
        return $this;
    }

    /**
     * Observer on changed Customer SegmentIds
     *
     * @param \Magento\Event\Observer $observer
     * @return void
     */
    public function changedCustomerSegmentIds(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return;
        }
        $segmentIds = is_array($observer->getSegmentIds()) ? $observer->getSegmentIds() : array();
        $segmentsIdsString = implode(',', $segmentIds);
        $this->_cookie->set(\Magento\FullPageCache\Model\Cookie::CUSTOMER_SEGMENT_IDS, $segmentsIdsString);
    }

    /**
     * Disabling full page caching using no-cache cookie
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function setNoCacheCookie(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $this->_cookie->set(\Magento\FullPageCache\Model\Processor\RestrictionInterface::NO_CACHE_COOKIE, '1', 0);
        return $this;
    }

    /**
     * Activating full page cache by deleting no-cache cookie
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function deleteNoCacheCookie(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $this->_cookie->set(\Magento\FullPageCache\Model\Processor\RestrictionInterface::NO_CACHE_COOKIE, null);
        return $this;
    }

    /**
     * Invalidate design changes cache when design change was added/deleted
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function invalidateDesignChange(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        /** @var $design \Magento\Core\Model\Design */
        $design = $observer->getEvent()->getObject();
        $cacheId = $this->_designRules->getCacheId($design->getStoreId());
        $this->_fpcCache->remove($cacheId);

        return $this;
    }
}
