<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @category   Enterprise
 * @package    Enterprise_CatalogEvent
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */



/**
 * Catalog Event model
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogEvent
 */
class Enterprise_CatalogEvent_Model_Observer
{
    /**
     * Store categories tree
     *
     * @var Varien_Data_Tree
     */
    protected $_storeCategories = null;

    /**
     * In controller pre dipatch observer called flag
     *
     * @var boolean
     */
    protected $_inControllerInit = false;

    protected $_eventApplyToProduct = array();

    /**
     * Applies event status by cron
     *
     * @return void
     */
    public function applyEventStatus()
    {
        $collection = Mage::getModel('enterprise_catalogevent/event')->getCollection();
        // We should check only not closed events.
        $collection->addFieldToFilter('status',
            array(

                'in' => array(

                    Enterprise_CatalogEvent_Model_Event::STATUS_OPEN,
                    Enterprise_CatalogEvent_Model_Event::STATUS_UPCOMING
                )
            ));
        foreach ($collection as $event) {
            /* @var $event Enterprise_CatalogEvent_Model_Event */
            try {
                $event->applyStatusByDates();
                if ($event->dataHasChangedFor('status')) {
                    // Save only if status was changed.
                    $event->save();
                }
            } catch (Exception $e) {    // Ignore
            }
        }
    }

    /**
     * Applies event to category
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function applyEventToCategory(Varien_Event_Observer $observer)
    {
        $category = $observer->getEvent()->getCategory();
        $categoryIds = $this->_parseCategoryPath($category->getPath());
        if (! empty($categoryIds)) {
            $eventCollection = $this->_getEventCollection($categoryIds);
            $this->_applyEventToCategory($category, $eventCollection);
        }
    }

    /**
     * Applies event to category collection
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function applyEventToCategoryCollection(Varien_Event_Observer $observer)
    {
        $categoryCollection = $observer->getEvent()->getCategoryCollection();
        /* @var $categoryCollection Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection */

        $categoryIds = array();

        foreach ($categoryCollection->getColumnValues('path') as $path) {
            $categoryIds = array_merge($categoryIds,
                $this->_parseCategoryPath($path));
        }

        if (! empty($categoryIds)) {
            $eventCollection = $this->_getEventCollection($categoryIds);
            foreach ($categoryCollection as $category) {
                $this->_applyEventToCategory($category,
                    $eventCollection);
            }
        }
    }

    /**
     * Applies event to product
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function applyEventToProduct(Varien_Event_Observer $observer)
    {
        if ($this->_inControllerInit) {
            $this->_eventApplyToProduct[] = $observer->getEvent()->getProduct();
        } else {
            $product = $observer->getEvent()->getProduct();
            $this->_applyEventToProduct($product);
        }
    }

    /**
     * Set flag for mass event loading for products
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function applyEventToProductOnPreDispatch()
    {
        $this->_inControllerInit = true;
    }

    /**
     * Applies events to product on layout load
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function applyEventToProductOnLoadLayout()
    {
        $this->_inControllerInit = false;
        if (Mage::registry('current_product')) {
            $product = Mage::registry('current_product');
            if (Mage::registry('current_category')) {
                $product->setCategoryId(Mage::registry('current_category')->getId());
            }
            $this->_applyEventToProduct($product);
        }

        if (!empty($this->_eventApplyToProduct)) {
            foreach ($this->_eventApplyToProduct as $product) {
                $this->_applyEventToProduct($product);
            }

            $this->_eventApplyToProduct = array();
        }

    }

    /**
     * Applies event to product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Enterprise_CatalogEvent_Model_Observer
     */
    protected function _applyEventToProduct($product)
    {
        if (!$product->hasEvent()) {
            $event = $this->_getProductEvent($product);
            $product->setEvent($event);
            if ($event && in_array($event->getStatus(), array(
                Enterprise_CatalogEvent_Model_Event::STATUS_CLOSED,
                Enterprise_CatalogEvent_Model_Event::STATUS_UPCOMING
            ))) {
                $product->setData('is_salable', false);
            }
        }

        return $this;
    }

    /**
     * Applies events to product collection
     *
     * @param Varien_Event_Observer $observer
     * @return void
     * @throws Mage_Core_Exception
     */
    public function applyEventOnQuoteItemSetProduct(Varien_Event_Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        /* @var $product Mage_Catalog_Model_Product */
        $quoteItem = $observer->getEvent()->getQuoteItem();
        /* @var $quoteItem Mage_Sales_Model_Quote_Item */
        if ($product->getEvent()) {
            $quoteItem->setEventId($product->getEvent()->getId());
            $category = $this->_getCategoryInStore($product->getEvent()->getCategoryId());
            if ($category) {
                $quoteItem->setEventName($category->getName());
            }
            if ($quoteItem->getParentItem()) {
                $quoteItem->getParentItem()->setEventId($quoteItem->getEventId())
                    ->setEventName($quoteItem->getEventName());
            }
            if ($product->getEvent()->getStatus() != Enterprise_CatalogEvent_Model_Event::STATUS_OPEN) {
                if (!$quoteItem->getId()) {
                    $quoteItem->setIsDeleted(true);
                } else {
                    $quoteItem->getQuote()->removeItem($quoteItem->getId());
                }

                Mage::throwException(
                    Mage::helper('enterprise_catalogevent')->__('Sale was closed for product "%s".', $quoteItem->getName())
                );
            }
        }
    }

    /**
     * Applies events to product collection
     *
     * @param Varien_Event_Observer $observer
     * @return void
     * @throws Mage_Core_Exception
     */
    public function applyEventOnQuoteItemSetQty(Varien_Event_Observer $observer)
    {

        $item = $observer->getEvent()->getItem();
        /* @var $item Mage_Sales_Model_Quote_Item */
        $this->_initializeEventsForQuoteItems($item->getQuote());
        if ($item->getEvent()
            && $item->getEvent()->getStatus() !== Enterprise_CatalogEvent_Model_Event::STATUS_OPEN) {
            $item->getQuote()->removeItem($item->getId());
            if ($item->getParentItem()) {
                $parentItem = $item->getParentItem();
                $item->getQuote()->setHasError(true)
                        ->addMessage(
                            Mage::helper('enterprise_catalogevent')->__('Sale was closed for product "%s".', $parentItem->getName())
                        );
            } else {
                 $item->getQuote()->setHasError(true)
                        ->addMessage(
                            Mage::helper('enterprise_catalogevent')->__('Sale was closed for product "%s".', $item->getName())
                        );
            }
        }
    }

    /**
     * Applies events to product collection
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function applyEventToProductCollection(Varien_Event_Observer $observer)
    {
        $collection = $observer->getEvent()->getCollection();
        foreach ($collection as $product) {
            $this->_applyEventToProduct($product);
        }
    }

    /**
     * Get event for product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Enterprise_CatalogEvent_Model_Event
     */
    protected function _getProductEvent($product)
    {
        if (($categoryId = $product->getCategoryId())) {
            $category = $this->_getCategoryInStore($categoryId);
            return $category->getEvent();
        } elseif ($product->getCategory()) {
            return $product->getCategory()->getEvent();
        } else {
            $categoryIds = $product->getCategoryIds();
            $event = false;
            $noOpenEvent = false;
            $eventCount = 0;
            foreach ($categoryIds as $categoryId) {
                $category = $this->_getCategoryInStore($categoryId);
                if (!$category) {
                    continue;
                }
                if ($category->getEvent()
                    && $category->getEvent()->getStatus() == Enterprise_CatalogEvent_Model_Event::STATUS_OPEN) {
                    $event = $category->getEvent();
                    $eventCount++;
                } elseif($category->getEvent()) {
                    $noOpenEvent = $category->getEvent();
                    $eventCount++;
                }

            }

            if ($eventCount > 1) {
                $product->setEventNoTicker(true);
            }

            return ($event ? $event : $noOpenEvent);
        }

        return false;
    }

    /**
     * Return store categories
     *
     * @return Varien_Data_Tree
     */
    protected function _getStoreCategories()
    {
        if ($this->_storeCategories === null) {
            $this->_storeCategories = Mage::helper('catalog/category')->getStoreCategories(false, true);
        }

        return $this->_storeCategories;
    }

    /**
     * Return category in store
     *
     * @param int $categoryId
     * @return Mage_Catalog_Model_Category|Varien_Data_Tree_Node
     */
    protected function _getCategoryInStore($categoryId)
    {
        if (Mage::registry('current_category')
            && Mage::registry('current_category')->getId() == $categoryId) {
            // If category already loaded for page, we don't need to load categories tree
            return Mage::registry('current_category');
        }

        $category = $this->_getStoreCategories()->getItemById($categoryId);
        return $category;
    }

    /**
     * Return event collection
     *
     * @param array $categoryIds
     * @return Enterprise_CatalogEvent_Model_Mysql4_Event_Collection
     */
    protected function _getEventCollection(array $categoryIds = null)
    {
        $collection = Mage::getModel('enterprise_catalogevent/event')->getCollection();
        if ($categoryIds !== null) {
            $collection->addFieldToFilter('category_id',
                array(
                    'in' => $categoryIds
                ));
        }

        return $collection;
    }

    /**
     * Initialize events for quote items
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return Enterprise_CatalogEvent_Model_Observer
     */
    protected function _initializeEventsForQuoteItems(Mage_Sales_Model_Quote $quote)
    {
        if (!$quote->getEventInitialized()) {
             $quote->setEventInitialized(true);
             $eventIds = array_diff(
                $quote->getItemsCollection()->getColumnValues('event_id'),
                array(0)
             );

             if (!empty($eventIds)) {
                 $collection = $this->_getEventCollection();
                 $collection->addFieldToFilter('event_id', array('in'=>$eventIds));
                 foreach ($collection as $event) {
                     $quote->getItemsCollection()->getItemByColumnValue(
                        'event_id', $event->getId()
                     )->setEvent($event);
                 }
             }
        }

        return $this;
    }

    /**
     * Parse categories ids from category path
     *
     * @param string $path
     * @return array
     */
    protected function _parseCategoryPath($path)
    {
        return explode('/', $path);
    }

    /**
     * Apply event to category
     *
     * @param Varien_Data_Tree_Node|Mage_Catalog_Model_Category $category
     * @param Varien_Data_Collection $eventCollection
     * @return Enterprise_CatalogEvent_Model_Observer
     */
    protected function _applyEventToCategory($category, Varien_Data_Collection $eventCollection)
    {
        foreach (array_reverse($this->_parseCategoryPath($category->getPath())) as $categoryId) { // Walk throught category path, search event for category
            $event = $eventCollection->getItemByColumnValue(
                'category_id', $categoryId);
            if ($event) {
                $category->setEvent($event);
                return $this;
            }
        }

        return $this;
    }


}
