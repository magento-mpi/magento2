<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Full page cache observer
 *
 * @category   Magento
 * @package    Magento_FullPageCache
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_FullPageCache_Model_Observer
{
    /*
     * Design exception key
     */
    const XML_PATH_DESIGN_EXCEPTION = 'design/package/ua_regexp';

    /**
     * Page Cache Processor
     *
     * @var Magento_FullPageCache_Model_Processor
     */
    protected $_processor;

    /**
     * Page Cache Config
     *
     * @var Magento_FullPageCache_Model_Config
     */
    protected $_config;

    /**
     * Is Enabled Full Page Cache
     *
     * @var bool
     */
    protected $_isEnabled;

    /**
     * @var Magento_Core_Model_Cache_StateInterface
     */
    protected $_cacheState;

    /**
     * @var Magento_FullPageCache_Model_Cookie
     */
    protected $_cookie;

    /**
     * FPC cache model
     *
     * @var Magento_FullPageCache_Model_Cache
     */
    protected $_fpcCache;

    /**
     * FPC processor restriction model
     *
     * @var Magento_FullPageCache_Model_Processor_RestrictionInterface
     */
    protected $_restriction;

    /**
     * Request identifier model
     *
     * @var Magento_FullPageCache_Model_Request_Identifier
     */
    protected $_requestIdentifier;

    /**
     * Design rules
     *
     * @var Magento_FullPageCache_Model_DesignPackage_Rules
     */
    protected $_designRules;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * @param Magento_FullPageCache_Model_Processor $processor
     * @param Magento_FullPageCache_Model_Request_Identifier $_requestIdentifier
     * @param Magento_FullPageCache_Model_Config $config
     * @param Magento_Core_Model_Cache_StateInterface $cacheState
     * @param Magento_FullPageCache_Model_Cache $fpcCache
     * @param Magento_FullPageCache_Model_Cookie $cookie
     * @param Magento_FullPageCache_Model_Processor_RestrictionInterface $restriction
     * @param Magento_FullPageCache_Model_DesignPackage_Rules $designRules
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     */
    public function __construct(
        Magento_FullPageCache_Model_Processor $processor,
        Magento_FullPageCache_Model_Request_Identifier $_requestIdentifier,
        Magento_FullPageCache_Model_Config $config,
        Magento_Core_Model_Cache_StateInterface $cacheState,
        Magento_FullPageCache_Model_Cache $fpcCache,
        Magento_FullPageCache_Model_Cookie $cookie,
        Magento_FullPageCache_Model_Processor_RestrictionInterface $restriction,
        Magento_FullPageCache_Model_DesignPackage_Rules $designRules,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Core_Model_Store_Config $coreStoreConfig
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_processor = $processor;
        $this->_config    = $config;
        $this->_cacheState = $cacheState;
        $this->_fpcCache = $fpcCache;
        $this->_cookie = $cookie;
        $this->_restriction = $restriction;
        $this->_requestIdentifier = $_requestIdentifier;
        $this->_designRules = $designRules;
        $this->_isEnabled = $this->_cacheState->isEnabled('full_page');
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
     * @param Magento_Event_Observer $observer
     * @return Magento_FullPageCache_Model_Observer
     */
    public function cacheResponse(Magento_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $frontController = $observer->getEvent()->getFront();
        $request = $frontController->getRequest();
        $response = $frontController->getResponse();
        $this->_saveDesignException();
        $this->_processor->processRequestResponse($request, $response);
        return $this;
    }

    /**
     * Check when cache should be disabled
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_FullPageCache_Model_Observer
     */
    public function processPreDispatch(Magento_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $action = $observer->getEvent()->getControllerAction();
        /* @var $request Magento_Core_Controller_Request_Http */
        $request = $action->getRequest();
        /**
         * Check if request will be cached
         */
        if ($this->_processor->canProcessRequest($request) && $this->_processor->getRequestProcessor($request)) {
            $this->_cacheState->setEnabled(Magento_Core_Block_Abstract::CACHE_GROUP, false); // disable blocks cache
            Mage::getSingleton('Magento_Catalog_Model_Session')->setParamsMemorizeDisabled(true);
        } else {
            Mage::getSingleton('Magento_Catalog_Model_Session')->setParamsMemorizeDisabled(false);
        }
        $this->_cookie->updateCustomerCookies();
        return $this;
    }

    /**
     * Checks whether exists design exception value in cache.
     * If not, gets it from config and puts into cache
     *
     * @return Magento_FullPageCache_Model_Observer
     */
    protected function _saveDesignException()
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $cacheId = Magento_FullPageCache_Model_DesignPackage_Info::DESIGN_EXCEPTION_KEY;
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
     * @param Magento_Event_Observer $observer
     * @return Magento_FullPageCache_Model_Observer
     */
    public function registerModelTag(Magento_Event_Observer $observer)
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
     * @param Magento_Event_Observer $observer
     * @return Magento_FullPageCache_Model_Observer
     */
    public function checkCategoryState(Magento_Event_Observer $observer)
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
     * @param Magento_Event_Observer $observer
     * @return Magento_FullPageCache_Model_Observer
     */
    public function checkProductState(Magento_Event_Observer $observer)
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
     * Check if data changes duering object save affect cached pages
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_FullPageCache_Model_Observer
     */
    public function validateDataChanges(Magento_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $object = $observer->getEvent()->getObject();
        $object = Mage::getModel('Magento_FullPageCache_Model_Validator')->checkDataChange($object);
        return $this;
    }

    /**
     * Check if data delete affect cached pages
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_FullPageCache_Model_Observer
     */
    public function validateDataDelete(Magento_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $object = $observer->getEvent()->getObject();
        $object = Mage::getModel('Magento_FullPageCache_Model_Validator')->checkDataDelete($object);
        return $this;
    }

    /**
     * Clean full page cache
     *
     * @return Magento_FullPageCache_Model_Observer
     */
    public function cleanCache()
    {
        $this->_fpcCache->clean(Magento_FullPageCache_Model_Processor::CACHE_TAG);
        return $this;
    }

    /**
     * Invalidate full page cache
     * @return Magento_FullPageCache_Model_Observer
     */
    public function invalidateCache()
    {
        /** @var Magento_Core_Model_Cache_TypeListInterface $cacheTypeList */
        $cacheTypeList = Mage::getObjectManager()->get('Magento_Core_Model_Cache_TypeListInterface');
        $cacheTypeList->invalidate('full_page');
        return $this;
    }

    /**
     * Render placeholder tags around the block if needed
     *
     * Event: core_layout_render_element
     * @param Magento_Event_Observer $observer
     * @return Magento_FullPageCache_Model_Observer
     */
    public function renderBlockPlaceholder(Magento_Event_Observer $observer)
    {
        if (!$this->_isEnabled) {
            return $this;
        }
        $event = $observer->getEvent();
        /** @var $layout Magento_Core_Model_Layout */
        $layout = $event->getData('layout');
        $name = $event->getData('element_name');
        if (!$layout->isBlock($name)) {
            return $this;
        }
        $block = $layout->getBlock($name);
        $transport = $event->getData('transport');
        $placeholder = $this->_config->getBlockPlaceholder($block);
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
     * @param Magento_Event_Observer $observer
     * @return Magento_FullPageCache_Model_Observer
     */
    public function registerQuoteChange(Magento_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        /** @var Magento_Sales_Model_Quote */
        $quote = ($observer->getEvent()->getQuote()) ? $observer->getEvent()->getQuote() :
            $observer->getEvent()->getQuoteItem()->getQuote();
        $this->_cookie->setObscure(Magento_FullPageCache_Model_Cookie::COOKIE_CART, 'quote_' . $quote->getId());

        $cacheId = Magento_FullPageCache_Model_Container_Advanced_Quote::getCacheId();
        $this->_fpcCache->remove($cacheId);

        return $this;
    }

    /**
     * Set compare list in cookie on list change. Also modify recently compared cookie.
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_FullPageCache_Model_Observer
     */
    public function registerCompareListChange(Magento_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }

        $listItems = Mage::helper('Magento_Catalog_Helper_Product_Compare')->getItemCollection();
        $previousList = $this->_cookie->get(Magento_FullPageCache_Model_Cookie::COOKIE_COMPARE_LIST);
        $previousList = (empty($previousList)) ? array() : explode(',', $previousList);

        $ids = array();
        foreach ($listItems as $item) {
            $ids[] = $item->getId();
        }
        sort($ids);
        $this->_cookie->set(Magento_FullPageCache_Model_Cookie::COOKIE_COMPARE_LIST, implode(',', $ids));

        //Recently compared products processing
        $recentlyComparedProducts = $this->_cookie
            ->get(Magento_FullPageCache_Model_Cookie::COOKIE_RECENTLY_COMPARED);
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

        $this->_cookie->set(Magento_FullPageCache_Model_Cookie::COOKIE_RECENTLY_COMPARED,
            implode(',', $recentlyComparedProducts));

       return $this;
    }

    /**
     * Set new message cookie on adding messsage to session.
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_FullPageCache_Model_Observer
     */
    public function processNewMessage(Magento_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $this->_cookie->set(Magento_FullPageCache_Model_Cookie::COOKIE_MESSAGE, '1');
        return $this;
    }


    /**
     * Update customer viewed products index and renew customer viewed product ids cookie
     *
     * @return Magento_FullPageCache_Model_Observer
     */
    public function updateCustomerProductIndex()
    {
        try {
            $productIds = $this->_cookie->get(Magento_FullPageCache_Model_Container_Viewedproducts::COOKIE_NAME);
            if ($productIds) {
                $productIds = explode(',', $productIds);
                Mage::getModel('Magento_Reports_Model_Product_Index_Viewed')->registerIds($productIds);
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }

        // renew customer viewed product ids cookie
        $countLimit = $this->_coreStoreConfig->getConfig(Magento_Reports_Block_Product_Viewed::XML_PATH_RECENTLY_VIEWED_COUNT);
        $collection = Mage::getResourceModel('Magento_Reports_Model_Resource_Product_Index_Viewed_Collection')
            ->addIndexFilter()
            ->setAddedAtOrder()
            ->setPageSize($countLimit)
            ->setCurPage(1)
            ->setVisibility(Mage::getSingleton('Magento_Catalog_Model_Product_Visibility')->getVisibleInSiteIds());

        $productIds = $collection->load()->getLoadedIds();
        $productIds = implode(',', $productIds);
        $this->_cookie->registerViewedProducts($productIds, $countLimit, false);
        return $this;
    }

    /**
     * Set cookie for logged in customer
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_FullPageCache_Model_Observer
     */
    public function customerLogin(Magento_Event_Observer $observer)
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
     * @param Magento_Event_Observer $observer
     * @return Magento_FullPageCache_Model_Observer
     */
    public function customerLogout(Magento_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $this->_cookie->updateCustomerCookies();

        if (!$this->_cookie->get(Magento_FullPageCache_Model_Cookie::COOKIE_CUSTOMER)) {
            $this->_cookie->delete(Magento_FullPageCache_Model_Cookie::COOKIE_RECENTLY_COMPARED);
            $this->_cookie->delete(Magento_FullPageCache_Model_Cookie::COOKIE_COMPARE_LIST);
            Magento_FullPageCache_Model_Cookie::registerViewedProducts(array(), 0, false);
        }

        return $this;
    }

    /**
     * Set wishlist hash in cookie on wishlist change
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_FullPageCache_Model_Observer
     */
    public function registerWishlistChange(Magento_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }

        $cookieValue = '';
        foreach (Mage::helper('Magento_Wishlist_Helper_Data')->getWishlistItemCollection() as $item) {
            $cookieValue .= ($cookieValue ? '_' : '') . $item->getId();
        }

        // Wishlist sidebar hash
        $this->_cookie->setObscure(Magento_FullPageCache_Model_Cookie::COOKIE_WISHLIST, $cookieValue);

        // Wishlist items count hash for top link
        $this->_cookie->setObscure(Magento_FullPageCache_Model_Cookie::COOKIE_WISHLIST_ITEMS,
            'wishlist_item_count_' . Mage::helper('Magento_Wishlist_Helper_Data')->getItemCount());

        return $this;
    }

    /**
     * Clear wishlist list
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_FullPageCache_Model_Observer
     */
    public function registerWishlistListChange(Magento_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $placeholder = Mage::getSingleton('Magento_FullPageCache_Model_Container_PlaceholderFactory')
            ->create('WISHLISTS');

        $blockContainer = Mage::getModel(
            'Magento_FullPageCache_Model_Container_Wishlists', array('placeholder' => $placeholder)
        );
        $this->_fpcCache->remove($blockContainer->getCacheId());

        return $this;
    }

    /**
     * Set poll hash in cookie on poll vote
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_FullPageCache_Model_Observer
     */
    public function registerPollChange(Magento_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }

        $cookieValue = $observer->getEvent()->getPoll()->getId();
        $this->_cookie->set(Magento_FullPageCache_Model_Cookie::COOKIE_POLL, $cookieValue);

        return $this;
    }

    /**
     * Clean order sidebar cache
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_FullPageCache_Model_Observer
     */
    public function registerNewOrder(Magento_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }

        // Customer order sidebar tag
        $cacheId = md5($this->_cookie->get(Magento_FullPageCache_Model_Cookie::COOKIE_CUSTOMER));
        $this->_fpcCache->remove($cacheId);
        return $this;
    }

    /**
     * Remove new message cookie on clearing session messages.
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_FullPageCache_Model_Observer
     */
    public function processMessageClearing(Magento_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $this->_cookie->delete(Magento_FullPageCache_Model_Cookie::COOKIE_MESSAGE);
        return $this;
    }

    /**
     * Resave exception rules to cache storage
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_FullPageCache_Model_Observer
     */
    public function registerDesignExceptionsChange(Magento_Event_Observer $observer)
    {
        $object = $observer->getDataObject();
        $this->_fpcCache->save($object->getValue(), Magento_FullPageCache_Model_DesignPackage_Info::DESIGN_EXCEPTION_KEY,
                array(Magento_FullPageCache_Model_Processor::CACHE_TAG));
        return $this;
    }

    /**
     * Update info about product on product page
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_FullPageCache_Model_Observer
     */
    public function updateProductInfo(Magento_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }

        $paramsObject = $observer->getEvent()->getParams();
        if ($paramsObject instanceof Magento_Object) {
            if (array_key_exists(Magento_FullPageCache_Model_Cookie::COOKIE_CATEGORY_ID, $_COOKIE)) {
                $paramsObject->setCategoryId($_COOKIE[Magento_FullPageCache_Model_Cookie::COOKIE_CATEGORY_ID]);
            }
        }
        return $this;
    }

    /**
     * Check cross-domain session messages
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_FullPageCache_Model_Observer
     */
    public function checkMessages(Magento_Event_Observer $observer)
    {
        $transport = $observer->getEvent()->getTransport();
        if (!$transport || !$transport->getUrl()) {
            return $this;
        }
        $url = $transport->getUrl();
        $httpHost = Mage::app()->getFrontController()->getRequest()->getHttpHost();
        $urlHost = parse_url($url, PHP_URL_HOST);
        if ($httpHost != $urlHost && Mage::getSingleton('Magento_Core_Model_Session')->getMessages()->count() > 0) {
            $transport->setUrl(Mage::helper('Magento_Core_Helper_Url')->addRequestParam(
                $url,
                array(Magento_FullPageCache_Model_Cache::REQUEST_MESSAGE_GET_PARAM => null)
            ));
        }
        return $this;
    }

    /**
     * Observer on changed Customer SegmentIds
     *
     * @param Magento_Event_Observer $observer
     * @return void
     */
    public function changedCustomerSegmentIds(Magento_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return;
        }
        $segmentIds = is_array($observer->getSegmentIds()) ? $observer->getSegmentIds() : array();
        $segmentsIdsString = implode(',', $segmentIds);
        $this->_cookie->set(Magento_FullPageCache_Model_Cookie::CUSTOMER_SEGMENT_IDS, $segmentsIdsString);
    }

    /**
     * Disabling full page caching using no-cache cookie
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_FullPageCache_Model_Observer
     */
    public function setNoCacheCookie(Magento_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $this->_cookie->set(Magento_FullPageCache_Model_Processor_RestrictionInterface::NO_CACHE_COOKIE, '1', 0);
        return $this;
    }

    /**
     * Activating full page cache by deleting no-cache cookie
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_FullPageCache_Model_Observer
     */
    public function deleteNoCacheCookie(Magento_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $this->_cookie->delete(Magento_FullPageCache_Model_Processor_RestrictionInterface::NO_CACHE_COOKIE);
        return $this;
    }

    /**
     * Invalidate design changes cache when design change was added/deleted
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_FullPageCache_Model_Observer
     */
    public function invalidateDesignChange(Magento_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        /** @var $design Magento_Core_Model_Design */
        $design = $observer->getEvent()->getObject();
        $cacheId = $this->_designRules->getCacheId($design->getStoreId());
        $this->_fpcCache->remove($cacheId);

        return $this;
    }
}
