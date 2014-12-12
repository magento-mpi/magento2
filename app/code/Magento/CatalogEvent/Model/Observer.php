<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CatalogEvent\Model;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\CatalogEvent\Helper\Data;
use Magento\CatalogEvent\Model\Category\EventList;
use Magento\Framework\Data\Collection;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Sales\Model\Quote;

/**
 * Catalog Event model
 */
class Observer
{
    /**
     * Catalog event data
     *
     * @var Data
     */
    protected $catalogEventData;

    /**
     * Event model factory
     *
     * @var \Magento\CatalogEvent\Model\Category\EventList
     */
    protected $categoryEventList;

    /**
     * Construct
     *
     * @param Data $catalogEventData
     * @param EventList $eventList
     */
    public function __construct(Data $catalogEventData, EventList $eventList)
    {
        $this->catalogEventData = $catalogEventData;
        $this->categoryEventList = $eventList;
    }

    /**
     * Applies event to category
     *
     * @param EventObserver $observer
     * @return void|$this
     */
    public function applyEventToCategory(EventObserver $observer)
    {
        if (!$this->catalogEventData->isEnabled()) {
            return $this;
        }

        $category = $observer->getEvent()->getCategory();
        $categoryIds = $this->_parseCategoryPath($category->getPath());
        if (!empty($categoryIds)) {
            $eventCollection = $this->categoryEventList->getEventCollection($categoryIds);
            $this->_applyEventToCategory($category, $eventCollection);
        }
    }

    /**
     * Applies event to category collection
     *
     * @param EventObserver $observer
     * @return void|$this
     */
    public function applyEventToCategoryCollection(EventObserver $observer)
    {
        if (!$this->catalogEventData->isEnabled()) {
            return $this;
        }

        /** @var $categoryCollection \Magento\Catalog\Model\Resource\Category\Collection */
        $categoryCollection = $observer->getEvent()->getCategoryCollection();
        $categoryIds = [];

        foreach ($categoryCollection->getColumnValues('path') as $path) {
            $categoryIds = array_merge($categoryIds, $this->_parseCategoryPath($path));
        }

        if (!empty($categoryIds)) {
            $eventCollection = $this->categoryEventList->getEventCollection($categoryIds);
            foreach ($categoryCollection as $category) {
                $this->_applyEventToCategory($category, $eventCollection);
            }
        }
    }

    /**
     * Applies event to product
     *
     * @param EventObserver $observer
     * @return void|$this
     */
    public function applyEventToProduct(EventObserver $observer)
    {
        if (!$this->catalogEventData->isEnabled()) {
            return $this;
        }

        $product = $observer->getEvent()->getProduct();
        $this->_applyEventToProduct($product);
    }

    /**
     * Apply is salable to product
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function applyIsSalableToProduct(EventObserver $observer)
    {
        $event = $observer->getEvent()->getProduct()->getEvent();
        if ($event && in_array($event->getStatus(), [Event::STATUS_CLOSED, Event::STATUS_UPCOMING])) {
            $observer->getEvent()->getSalable()->setIsSalable(false);
        }
        return $this;
    }

    /**
     * Applies event to product
     *
     * @param Product $product
     * @return $this
     */
    protected function _applyEventToProduct($product)
    {
        if ($product && !$product->hasEvent()) {
            $event = $this->_getProductEvent($product);
            $product->setEvent($event);
        }
        return $this;
    }

    /**
     * Applies events to product collection
     *
     * @param EventObserver $observer
     * @return void|$this
     */
    public function applyEventOnQuoteItemSetProduct(EventObserver $observer)
    {
        if (!$this->catalogEventData->isEnabled()) {
            return $this;
        }

        /* @var $product \Magento\Catalog\Model\Product */
        $product = $observer->getEvent()->getProduct();
        /* @var $quoteItem \Magento\Sales\Model\Quote\Item */
        $quoteItem = $observer->getEvent()->getQuoteItem();

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
     * @param EventObserver $observer
     * @return void|$this
     */
    public function applyEventOnQuoteItemSetQty(EventObserver $observer)
    {
        if (!$this->catalogEventData->isEnabled()) {
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
                if ($event->getStatus() !== Event::STATUS_OPEN) {
                    $item->setHasError(true)->setMessage(__('The sale for this product is closed.'));
                    $item->getQuote()->setHasError(
                        true
                    )->addMessage(
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
     * @param EventObserver $observer
     * @return void|$this
     */
    public function applyEventToProductCollection(EventObserver $observer)
    {
        if (!$this->catalogEventData->isEnabled()) {
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
     * @param Product $product
     * @return Event|false|null
     */
    protected function _getProductEvent($product)
    {
        if (!$product instanceof Product) {
            return false;
        }

        $categoryIds = $product->getCategoryIds();

        $event = false;
        $noOpenEvent = false;
        $eventCount = 0;
        foreach ($categoryIds as $categoryId) {
            $categoryEvent = $this->categoryEventList->getEventInStore($categoryId);
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

        return $event ? $event : $noOpenEvent;
    }

    /**
     * Initialize events for quote items
     *
     * @param Quote $quote
     * @return $this
     */
    protected function _initializeEventsForQuoteItems(Quote $quote)
    {
        if (!$quote->getEventInitialized()) {
            $quote->setEventInitialized(true);
            $eventIds = array_diff($quote->getItemsCollection()->getColumnValues('event_id'), [0]);

            if (!empty($eventIds)) {
                $collection = $this->categoryEventList->getEventCollection();
                $collection->addFieldToFilter('event_id', ['in' => $eventIds]);
                foreach ($collection as $event) {
                    $items = $quote->getItemsCollection()->getItemsByColumnValue('event_id', $event->getId());
                    foreach ($items as $quoteItem) {
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
     * @return string[]
     */
    protected function _parseCategoryPath($path)
    {
        return explode('/', $path);
    }

    /**
     * Apply event to category
     *
     * @param Node|Category $category
     * @param Collection $eventCollection
     * @return $this
     */
    protected function _applyEventToCategory($category, Collection $eventCollection)
    {
        foreach (array_reverse($this->_parseCategoryPath($category->getPath())) as $categoryId) {
            // Walk through category path, search event for category
            $event = $eventCollection->getItemByColumnValue('category_id', $categoryId);
            if ($event) {
                $category->setEvent($event);
                return $this;
            }
        }

        return $this;
    }
}
