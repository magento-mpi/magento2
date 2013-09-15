<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogEvent
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Event model
 */
namespace Magento\CatalogEvent\Model;

class Observer
{
    /**
     * Store categories events
     *
     * @var array
     */
    protected $_eventsToCategories = null;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;
    
    /**
     * Catalog event data
     *
     * @var Magento_CatalogEvent_Helper_Data
     */
    protected $_catalogEventData = null;

    /**
     * @param Magento_CatalogEvent_Helper_Data $catalogEventData
     * @param Magento_Core_Model_Registry $coreRegistry
     */
    public function __construct(
        Magento_CatalogEvent_Helper_Data $catalogEventData,
        Magento_Core_Model_Registry $coreRegistry
    ) {
        $this->_catalogEventData = $catalogEventData;
        $this->_coreRegistry = $coreRegistry;
    }

    /**
     * Applies event to category
     *
     * @param \Magento\Event\Observer $observer
     * @return void
     */
    public function applyEventToCategory(\Magento\Event\Observer $observer)
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
     * @param \Magento\Event\Observer $observer
     * @return void
     */
    public function applyEventToCategoryCollection(\Magento\Event\Observer $observer)
    {
        if (!$this->_catalogEventData->isEnabled()) {
            return $this;
        }

        $categoryCollection = $observer->getEvent()->getCategoryCollection();
        /** @var $categoryCollection \Magento\Catalog\Model\Resource\Category\Collection */

        $categoryIds = array();

        foreach ($categoryCollection->getColumnValues('path') as $path) {
            $categoryIds = array_merge($categoryIds,
                $this->_parseCategoryPath($path));
        }

        if (!empty($categoryIds)) {
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
     * @param \Magento\Event\Observer $observer
     * @return void
     */
    public function applyEventToProduct(\Magento\Event\Observer $observer)
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
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CatalogEvent\Model\Observer
     */
    public function applyIsSalableToProduct(\Magento\Event\Observer $observer)
    {
        $event = $observer->getEvent()->getProduct()->getEvent();
        if ($event && in_array($event->getStatus(), array(
                    \Magento\CatalogEvent\Model\Event::STATUS_CLOSED,
                    \Magento\CatalogEvent\Model\Event::STATUS_UPCOMING
        ))) {
            $observer->getEvent()->getSalable()->setIsSalable(false);
        }
        return $this;
    }

    /**
     * Applies event to product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\CatalogEvent\Model\Observer
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
     * @param \Magento\Event\Observer $observer
     * @return void
     * @throws \Magento\Core\Exception
     */
    public function applyEventOnQuoteItemSetProduct(\Magento\Event\Observer $observer)
    {
        if (!$this->_catalogEventData->isEnabled()) {
            return $this;
        }

        $product = $observer->getEvent()->getProduct();
        /* @var $product \Magento\Catalog\Model\Product */
        $quoteItem = $observer->getEvent()->getQuoteItem();
        /* @var $quoteItem \Magento\Sales\Model\Quote\Item */

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
     * @param \Magento\Event\Observer $observer
     * @return void
     * @throws \Magento\Core\Exception
     */
    public function applyEventOnQuoteItemSetQty(\Magento\Event\Observer $observer)
    {
        if (!$this->_catalogEventData->isEnabled()) {
            return $this;
        }

        $item = $observer->getEvent()->getItem();
        /* @var $item \Magento\Sales\Model\Quote\Item */
        if ($item->getQuote()) {
            $this->_initializeEventsForQuoteItems($item->getQuote());
        }

        if ($item->getEventId()) {
            $event = $item->getEvent();
            if ($event) {
                if ($event->getStatus() !== \Magento\CatalogEvent\Model\Event::STATUS_OPEN) {
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
     * @param \Magento\Event\Observer $observer
     * @return void
     */
    public function applyEventToProductCollection(\Magento\Event\Observer $observer)
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
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\CatalogEvent\Model\Event
     */
    protected function _getProductEvent($product)
    {
        if (!$product instanceof \Magento\Catalog\Model\Product) {
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
            } elseif ($categoryEvent->getStatus() == \Magento\CatalogEvent\Model\Event::STATUS_OPEN) {
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
     * @return \Magento\CatalogEvent\Model\Event
     */
    protected function _getEventInStore($categoryId)
    {
        if ($this->_coreRegistry->registry('current_category')
            && $this->_coreRegistry->registry('current_category')->getId() == $categoryId) {
            // If category already loaded for page, we don't need to load categories tree
            return $this->_coreRegistry->registry('current_category')->getEvent();
        }

        if ($this->_eventsToCategories === null) {
            $this->_eventsToCategories = \Mage::getModel('Magento\CatalogEvent\Model\Event')->getCategoryIdsWithEvent(
                \Mage::app()->getStore()->getId()
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
     * @return \Magento\CatalogEvent\Model\Resource\Event\Collection
     */
    protected function _getEventCollection(array $categoryIds = null)
    {
        $collection = \Mage::getModel('Magento\CatalogEvent\Model\Event')->getCollection();
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
     * @param \Magento\Sales\Model\Quote $quote
     * @return \Magento\CatalogEvent\Model\Observer
     */
    protected function _initializeEventsForQuoteItems(\Magento\Sales\Model\Quote $quote)
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
     * @param \Magento\Data\Tree\Node|\Magento\Catalog\Model\Category $category
     * @param \Magento\Data\Collection $eventCollection
     * @return \Magento\CatalogEvent\Model\Observer
     */
    protected function _applyEventToCategory($category, \Magento\Data\Collection $eventCollection)
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
