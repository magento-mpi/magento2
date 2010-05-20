<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_PageCache
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_PageCache_Model_Observer
{
    const CUSTOMER_COOKIE_NAME = 'CUSTOMER_INFO';
    const CACHE_KEY = 'full_page_cache_key';

    /**
     * @var Enterprise_PageCache_Model_Processor
     */
    protected $_processor;
    protected $_config;
    protected $_isEnabled;

    /**
     * Full page cache encryption key
     *
     * @var sting
     */
    protected $_encryptionKey = null;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_processor = Mage::getModel('enterprise_pagecache/processor');
        $this->_config    = Mage::getSingleton('enterprise_pagecache/config');
        $this->_isEnabled = Mage::app()->useCache('full_page');

        if ($key = Mage::app()->getCache()->load(self::CACHE_KEY)) {
            $this->_encryptionKey = $key;
        } else {
             $this->_encryptionKey = md5(time() . rand());
             Mage::app()->getCache()->save($this->_encryptionKey, self::CACHE_KEY,
                array(Enterprise_PageCache_Model_Processor::CACHE_TAG), false);
        }
    }

    /**
     * Check if full page cache is enabled
     * @return bool
     */
    public function isCacheEnabled()
    {
        return $this->_isEnabled;
    }

    /**
     * Save page body to cache storage
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_PageCache_Model_Observer
     */
    public function cacheResponse(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $frontController = $observer->getEvent()->getFront();
        $request = $frontController->getRequest();
        $response = $frontController->getResponse();
        $this->_processor->processRequestResponse($request, $response);
        return $this;
    }

    /**
     * Check when cache should be disabled
     *
     * @param $observer
     */
    public function processPreDispatch(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $action = $observer->getEvent()->getControllerAction();
        /* @var $request Mage_Core_Controller_Request_Http */
        $request = $action->getRequest();
        /* @var $cookie Mage_Core_Model_Cookie */
        $cookie = Mage::getSingleton('core/cookie');
        $cookieName = Enterprise_PageCache_Model_Processor::NO_CACHE_COOKIE;
        $noCache = $cookie->get($cookieName);
        if ($noCache) {
            Mage::getSingleton('catalog/session')->setParamsMemorizeDisabled(false);
            $cookie->renew($cookieName);
        } elseif ($action) {
            Mage::getSingleton('catalog/session')->setParamsMemorizeDisabled(true);
        }
        /**
         * Check if request will be cached
         */
        if ($this->_processor->canProcessRequest($request)) {
            Mage::app()->setUseSessionInUrl(false); // disable SID
            Mage::app()->getCacheInstance()->banUse(Mage_Core_Block_Abstract::CACHE_GROUP); // disable blocks cache
        }
        return $this;
    }

    /**
     * model_load_after event processor. Collect tags of all loaded entities
     *
     * @param $observer
     */
    public function registerModelTag(Varien_Event_Observer $observer)
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
    }

    /**
     * Check category state on post dispatch to allow category page be cached
     *
     * @param Varien_Event_Observer $observer
     */
    public function checkCategoryState(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $category = Mage::registry('current_category');
        /**
         * Categories with category event can't be cached
         */
        if ($category && $category->getEvent()) {
            $request = $observer->getEvent()->getControllerAction()->getRequest();
            $request->setParam('no_cache', true);
        }
        return $this;
    }

    /**
     * Check product state on post dispatch to allow product page be cached
     *
     * @param Varien_Event_Observer $observer
     */
    public function checkProductState(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $product = Mage::registry('current_product');
        /**
         * Categories with category event can't be cached
         */
        if ($product && $product->getEvent()) {
            $request = $observer->getEvent()->getControllerAction()->getRequest();
            $request->setParam('no_cache', true);
        }
        return $this;
    }

    /**
     * Check if data changes duering object save affect cached pages
     *
     * @param Varien_Event_Observer $observer
     */
    public function validateDataChanges(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $object = $observer->getEvent()->getObject();
        $object = Mage::getModel('enterprise_pagecache/validator')->checkDataChange($object);
    }

    /**
     * Check if data delete affect cached pages
     *
     * @param Varien_Event_Observer $observer
     */
    public function validateDataDelete(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $object = $observer->getEvent()->getObject();
        $object = Mage::getModel('enterprise_pagecache/validator')->checkDataDelete($object);
    }


    /**
     * Clean full page cache
     */
    public function cleanCache()
    {
        Mage::app()->cleanCache(Enterprise_PageCache_Model_Processor::CACHE_TAG);
        return $this;
    }

    /**
     * Invalidate full page cache
     */
    public function invalidateCache()
    {
        Mage::app()->getCacheInstance()->invalidateType('full_page');
        return $this;
    }

    /**
     * Render placeholder tags around the block if needed
     *
     * @param Varien_Event_Observer $observer
     */
    public function renderBlockPlaceholder(Varien_Event_Observer $observer)
    {
        if (!$this->_isEnabled) {
            return $this;
        }
        $block = $observer->getEvent()->getBlock();
        $transport = $observer->getEvent()->getTransport();
        $placeholder = $this->_config->getBlockPlaceholder($block);
        if ($transport && $placeholder) {
            $blockHtml = $transport->getHtml();
            $blockHtml = $placeholder->getStartTag() . $blockHtml . $placeholder->getEndTag();
            $transport->setHtml($blockHtml);
        }
        return $this;
    }

    /**
     * Check cache settings for specific block type and associate block to container if needed
     *
     * @param Varien_Event_Observer $observer
     * @deprecated after 1.4.1.0-alpha1
     */
    public function blockCreateAfter(Varien_Event_Observer $observer)
    {
        if (!$this->_isEnabled) {
            return $this;
        }
        $block  = $observer->getEvent()->getBlock();
        $placeholder = $this->_config->getBlockPlaceholder($block);
        if ($placeholder) {
            $block->setFrameTags($placeholder->getStartTag(), $placeholder->getEndTag());
        }
        return $this;
    }

    /**
     * Set cart hash in cookie on quote change
     *
     * @param Varien_Event_Observer $observer
     */
    public function registerQuoteChange(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        /** @var Mage_Sales_Model_Quote */
        $quote = ($observer->getEvent()->getQuote()) ? $observer->getEvent()->getQuote() :
            $observer->getEvent()->getQuoteItem()->getQuote();
        $identificator = md5('quote_' . $quote->getId());
        Mage::getSingleton('core/cookie')->set(Enterprise_PageCache_Model_Container_CartSidebar::CART_COOKIE,
            $identificator);

        Mage::app()->getCache()->clean(Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG, array($identificator));

        return $this;
    }

    /**
     * Set compare list in cookie on list change. Also modify recently compared cookie.
     *
     * @param Varien_Event_Observer $observer
     */
    public function registerCompareListChange(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }

        $listItems = Mage::helper('catalog/product_compare')->getItemCollection();
        $previouseList = Mage::getSingleton('core/cookie')
            ->get(Enterprise_PageCache_Model_Container_CompareListSidebar::COOKIE);
        $previouseList = (empty($previouseList)) ? array() : explode(',', $previouseList);

        $ids = array();
        foreach ($listItems as $item) {
            $ids[] = $item->getId();
        }
        sort($ids);
        Mage::getSingleton('core/cookie')->set(Enterprise_PageCache_Model_Container_CompareListSidebar::COOKIE,
            implode(',', $ids));

        //Recenlty compared products processing
        $recentlyComparedProducts = Mage::getSingleton('core/cookie')
            ->get(Enterprise_PageCache_Model_Container_RecentlyComparedSidebar::COOKIE);
        $recentlyComparedProducts = (empty($recentlyComparedProducts)) ? array()
            : explode(',', $recentlyComparedProducts);

        //Adding products deleted from compare list to "recently compared products"
        $deletedProducts = array_diff($previouseList, $ids);
        $recentlyComparedProducts = array_merge($recentlyComparedProducts, $deletedProducts);

        //Removing products from recently product list if it's present in compare list
        $addedProducts = array_diff($ids, $previouseList);
        $recentlyComparedProducts = array_diff($recentlyComparedProducts, $addedProducts);

        $recentlyComparedProducts = array_unique($recentlyComparedProducts);
        sort($recentlyComparedProducts);

        Mage::getSingleton('core/cookie')->set(Enterprise_PageCache_Model_Container_RecentlyComparedSidebar::COOKIE,
            implode(',', $recentlyComparedProducts));

       return $this;
    }

    /**
     * Set new message cookie on adding messsage to session.
     *
     * @param Varien_Event_Observer $observer
     */
    public function processNewMessage(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        Mage::getSingleton('core/cookie')->set(Enterprise_PageCache_Model_Container_Messages::COOKIE, '1');
        return $this;
    }

    /**
     * Set cookie for logged in customer
     *
     * @param Varien_Event_Observer $observer
     */
    public function setCustomerCookie(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = $observer->getEvent()->getCustomer();
        $cookie = Mage::getModel('core/cookie');
        $cookie->set(Enterprise_PageCache_Model_Container_Welcome::COOKIE,
            md5($this->_encryptionKey . $customer->getId()));

        $cookieValue = md5($this->_encryptionKey . $customer->getGroupId());
        $cookie->set(self::CUSTOMER_COOKIE_NAME, $cookieValue);

        return $this;

    }

    /**
     * Remove customer cookie
     *
     * @param Varien_Event_Observer $observer
     */
    public function removeCustomerCookie(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = $observer->getEvent()->getCustomer();
        /** @var Mage_Core_Model_Cookie $cookie */
        $cookie = Mage::getModel('core/cookie');

        $cookie->delete(Enterprise_PageCache_Model_Container_Welcome::COOKIE);
        $cookie->delete(self::CUSTOMER_COOKIE_NAME);

        return $this;
    }

    /**
     * Set wishlist hash in cookie on wishlist change
     *
     * @param Varien_Event_Observer $observer
     */
    public function registerWishlistChange(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }

        $hash = '';
        /** @var Mage_Wishlist_Model_Mysql4_Product_Collection */
        $products = Mage::helper('wishlist')->getProductCollection();
        foreach ($products as $item) {
            $hash .= $item->getId() . '_';
        }
        /** @var Mage_Core_Model_Cookie $cookie */
        $cookie = Mage::getModel('core/cookie');
        $hash = md5($hash . $cookie->get(self::CUSTOMER_COOKIE_NAME));
        //Wishlist sidebar hash
        $cookie->set(Enterprise_PageCache_Model_Container_Wishlist::COOKIE, $hash);
        //Wishlist items count hash for top link
        $cookie->set(Enterprise_PageCache_Model_Container_WishlistLinks::COOKIE,
            md5($this->_encryptionKey . Mage::helper('wishlist')->getItemCount()));

        return $this;
    }

    /**
     * Clean order sidebar cache
     *
     * @param Varien_Event_Observer $observer
     */
    public function registerNewOrder(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }

        //Customer order sidebar tag
        $hash = Enterprise_PageCache_Model_Container_Orders::CACHE_TAG_PREFIX
            . Mage::getModel('core/cookie')->get(Enterprise_PageCache_Model_Container_Welcome::COOKIE);

        Mage::app()->getCache()->clean(Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG, array($hash));
        return $this;
    }



    /**
     * Check if last viewed product id should be processed after cached product view
     * @deprecated after 1.8 - added dynamic block generation
     */
    protected function _checkViewedProducts()
    {
        $varName = Enterprise_PageCache_Model_Processor::LAST_PRODUCT_COOKIE;
        $productId = (int) Mage::getSingleton('core/cookie')->get($varName);
        if ($productId) {
            $model = Mage::getModel('reports/product_index_viewed');
            if (!$model->getCount()) {
                $product = Mage::getModel('catalog/product')->load($productId);
                if ($product->getId()) {
                    $model->setProductId($productId)
                        ->save()
                        ->calculate();
                }
            }
            Mage::getSingleton('core/cookie')->delete($varName);
        }
    }
}
