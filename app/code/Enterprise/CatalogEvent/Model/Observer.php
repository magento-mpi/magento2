<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CatalogEvent
 * @copyright   {copyright}
 * @license     {license_link}
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
     * Store categories events
     *
     * @var array
     */
    protected $_eventsToCategories = null;

    /**
     * Catalog event data
     *
     * @var Enterprise_CatalogEvent_Helper_Data
     */
    protected $_catalogEventData = null;

    /**
     * @param Enterprise_CatalogEvent_Helper_Data $catalogEventData
     */
    public function __construct(
        Enterprise_CatalogEvent_Helper_Data $catalogEventData
    ) {
        $this->_catalogEventData = $catalogEventData;
    }

    /**
     * Applies event to category
     *
     * @param Magento_Event_Observer $observer
     * @return void
     */
    public function applyEventToCategory(Magento_Event_Observer $observer)
    {
        if (!$this->_catalogEventData->isEnabled()) {
            return $this;
        }

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
     * @param Magento_Event_Observer $observer
     * @return void
     */
    public function applyEventToCategoryCollection(Magento_Event_Observer $observer)
    {
        if (!$this->_catalogEventData->isEnabled()) {
            return $this;
        }

        $categoryCollection = $observer->getEvent()->getCategoryCollection();
        /** @var $categoryCollection Magento_Catalog_Model_Resource_Category_Collection */

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
     * @param Magento_Event_Observer $observer
     * @return void
     */
    public function applyEventToProduct(Magento_Event_Observer $observer)
    {
        if (!$this->_catalogEventData->isEnabled()) {
            return $this;
        }

        $product = $observer->getEvent()->getProduct();
        $this->_applyEventToProduct($product);
    }

    /**
     * Apply is salable to product
     *
     * @param Magento_Event_Observer $observer
     * @return Enterprise_CatalogEvent_Model_Observer
     */
    public function applyIsSalableToProduct(Magento_Event_Observer $observer)
    {
        $event = $observer->getEvent()->getProduct()->getEvent();
        if ($event && in_array($event->getStatus(), array(
                    Enterprise_CatalogEvent_Model_Event::STATUS_CLOSED,
                    Enterprise_CatalogEvent_Model_Event::STATUS_UPCOMING
        ))) {
            $observer->getEvent()->getSalable()->setIsSalable(false);
        }
        return $this;
    }

    /**
     * Applies event to product
     *
     * @param Magento_Catalog_Model_Product $product
     * @return Enterprise_CatalogEvent_Model_Observer
     */
    protected function _applyEventToProduct($product)
    {
        if ($product) {
            if (!$product->hasEvent()) {
                $event = $this->_getProductEvent($product);
                $product->setEvent($event);
            }
        }
        return $this;
    }

    /**
     * Applies events to product collection
     *
     * @param Magento_Event_Observer $observer
     * @return void
     * @throws Magento_Core_Exception
     */
    public function applyEventOnQuoteItemSetProduct(Magento_Event_Observer $observer)
    {
        if (!$this->_catalogEventData->isEnabled()) {
            return $this;
        }

        $product = $observer->getEvent()->getProduct();
        /* @var $product Magento_Catalog_Model_Product */
        $quoteItem = $observer->getEvent()->getQuoteItem();
        /* @var $quoteItem Magento_Sales_Model_Quote_Item */

        $this->_applyEventToProduct($product);

        if ($product->getEvent()) {
            $quoteItem->setEventId($product->getEvent()->getId());
            if ($quoteItem->getParentItem()) {
                $quoteItem->getParentItem()->setEventId($quoteItem->getEventId());
            }
        }
    }

    /**
     * Applies events to product collection
     *
     * @param Magento_Event_Observer $observer
     * @return void
     * @throws Magento_Core_Exception
     */
    public function applyEventOnQuoteItemSetQty(Magento_Event_Observer $observer)
    {
        if (!$this->_catalogEventData->isEnabled()) {
            return $this;
        }

        $item = $observer->getEvent()->getItem();
        /* @var $item Magento_Sales_Model_Quote_Item */
        if ($item->getQuote()) {
            $this->_initializeEventsForQuoteItems($item->getQuote());
        }

        if ($item->getEventId()) {
            if ($event = $item->getEvent()) {
                if ($event->getStatus() !== Enterprise_CatalogEvent_Model_Event::STATUS_OPEN) {
                    $item->setHasError(true)
                        ->setMessage(
                            __('The sale for this product is closed.')
                        );
                    $item->getQuote()->setHasError(true)
                        ->addMessage(
                            __('Some of these products can no longer be sold.')
                        );
                }
            } else {
                /*
                 * If quote item has event id but event was
                 * not assigned to it then we should set event id to
                 * null as event was removed already
                 */
                $item->setEventId(null);
            }
        }
    }

    /**
     * Applies events to product collection
     *
     * @param Magento_Event_Observer $observer
     * @return void
     */
    public function applyEventToProductCollection(Magento_Event_Observer $observer)
    {
        if (!$this->_catalogEventData->isEnabled()) {
            return $this;
        }

        $collection = $observer->getEvent()->getCollection();
        $collection->addCategoryIds();
        foreach ($collection as $product) {
            $this->_applyEventToProduct($product);
        }
    }

    /**
     * Get event for product
     *
     * @param Magento_Catalog_Model_Product $product
     * @return Enterprise_CatalogEvent_Model_Event
     */
    protected function _getProductEvent($product)
    {
        if (!$product instanceof Magento_Catalog_Model_Product) {
            return false;
        }

        $categoryIds = $product->getCategoryIds();

        $event = false;
        $noOpenEvent = false;
        $eventCount = 0;
        foreach ($categoryIds as $categoryId) {
            $categoryEvent = $this->_getEventInStore($categoryId);
            if ($categoryEvent === false) {
                continue;
            } elseif ($categoryEvent === null) {
                // If product assigned to category without event
                return null;
            } elseif ($categoryEvent->getStatus() == Enterprise_CatalogEvent_Model_Event::STATUS_OPEN) {
               $event = $categoryEvent;
            } else {
                $noOpenEvent = $categoryEvent;
            }
            $eventCount++;
        }

        if ($eventCount > 1) {
            $product->setEventNoTicker(true);
        }

        return ($event ? $event : $noOpenEvent);
    }


    /**
     * Get event in store
     *
     * @param int $categoryId
     * @return Enterprise_CatalogEvent_Model_Event
     */
    protected function _getEventInStore($categoryId)
    {
        if (Mage::registry('current_category')
            && Mage::registry('current_category')->getId() == $categoryId) {
            // If category already loaded for page, we don't need to load categories tree
            return Mage::registry('current_category')->getEvent();
        }

        if ($this->_eventsToCategories === null) {
            $this->_eventsToCategories = Mage::getModel('Enterprise_CatalogEvent_Model_Event')->getCategoryIdsWithEvent(
                Mage::app()->getStore()->getId()
            );

            $eventCollection = $this->_getEventCollection(array_keys($this->_eventsToCategories));

            foreach ($this->_eventsToCategories as $catId => $eventId) {
                if ($eventId !== null) {
                    $this->_eventsToCategories[$catId] = $eventCollection->getItemById($eventId);
                }
            }
        }

        if (isset($this->_eventsToCategories[$categoryId])) {
            return $this->_eventsToCategories[$categoryId];
        }

        return false;
    }

    /**
     * Return event collection
     *
     * @param array $categoryIds
     * @return Enterprise_CatalogEvent_Model_Resource_Event_Collection
     */
    protected function _getEventCollection(array $categoryIds = null)
    {
        $collection = Mage::getModel('Enterprise_CatalogEvent_Model_Event')->getCollection();
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
     * @param Magento_Sales_Model_Quote $quote
     * @return Enterprise_CatalogEvent_Model_Observer
     */
    protected function _initializeEventsForQuoteItems(Magento_Sales_Model_Quote $quote)
    {
        if (!$quote->getEventInitialized()) {
             $quote->setEventInitialized(true);
             $eventIds = array_diff(
                $quote->getItemsCollection()->getColumnValues('event_id'),
                array(0)
             );

             if (!empty($eventIds)) {
                 $collection = $this->_getEventCollection();
                 $collection->addFieldToFilter('event_id', array('in' => $eventIds));
                 foreach ($collection as $event) {
                     foreach ($quote->getItemsCollection()->getItemsByColumnValue(
                                  'event_id', $event->getId()
                              ) as $quoteItem) {
                        $quoteItem->setEvent($event);
                     }
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
     * @param Magento_Data_Tree_Node|Magento_Catalog_Model_Category $category
     * @param Magento_Data_Collection $eventCollection
     * @return Enterprise_CatalogEvent_Model_Observer
     */
    protected function _applyEventToCategory($category, Magento_Data_Collection $eventCollection)
    {
        foreach (array_reverse($this->_parseCategoryPath($category->getPath())) as $categoryId) { // Walk through category path, search event for category
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
