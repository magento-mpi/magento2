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
    /**
     * @var Enterprise_PageCache_Model_Processor
     */
    protected $_processor;

    protected $_cacheDisableActions = array(
        'checkout_cart_add',
        'catalog_product_compare_add',
    );

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_processor = Mage::getModel('enterprise_pagecache/processor');
    }

    /**
     * Save page body to cache storage
     *
     * @param Varien_Event_Observer $observer
     */
    public function cacheResponse(Varien_Event_Observer $observer)
    {
        $frontController = $observer->getEvent()->getFront();
        $request = $frontController->getRequest();
        if ($this->_processor->canProcessRequest($request)) {
            $respose = $frontController->getResponse();
            $this->_processor->process($respose);
        }
    }

    /**
     * Check when cache should be disabled
     *
     * @param $observer
     */
    public function processPreDispatch(Varien_Event_Observer $observer)
    {
        $action = $observer->getEvent()->getControllerAction();
        /* @var $request Mage_Core_Controller_Request_Http */
        $request = $action->getRequest();
        /* @var $cookie Mage_Core_Model_Cookie */
        $cookie = Mage::getSingleton('core/cookie');
        $cookieName = Enterprise_PageCache_Model_Processor::NO_CACHE_COOKIE;
        $noCache = $cookie->get($cookieName);
        if ($noCache) {
            $cookie->renew($cookieName);
        } elseif ($action) {
            if ($request->isPost()) {
                $cookie->set($cookieName, 1);
            } elseif (in_array($action->getFullActionName(), $this->_cacheDisableActions)) {
                $cookie->set(Enterprise_PageCache_Model_Processor::NO_CACHE_COOKIE, 1);
            }
        }
        /**
         * Check if request will be cached
         */
        if ($this->_processor->canProcessRequest($request)) {
            Mage::app()->setUseSessionInUrl(false); // disable SID
            Mage::app()->getCacheInstance()->banUse(Mage_Core_Block_Abstract::CACHE_GROUP); // disable blocks cache
        }
        $this->_checkViewedProducts();
        return $this;
    }

    /**
     * Check if last viewed product id should be processed after cached product view
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

    /**
     * model_load_after event processor. Collect tags of all loaded entities
     *
     * @param $observer
     */
    public function registerModelTag(Varien_Event_Observer $observer)
    {
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

}
