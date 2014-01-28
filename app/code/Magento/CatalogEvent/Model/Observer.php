<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Event model
 */
namespace Magento\CatalogEvent\Model;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\CatalogEvent\Helper\Data;
use Magento\CatalogEvent\Model\Resource\EventFactory;
use Magento\CatalogEvent\Model\Resource\Event\Collection as EventCollection;
use Magento\CatalogEvent\Model\Resource\Event\CollectionFactory;
use Magento\Core\Model\Registry;
use Magento\Core\Model\StoreManagerInterface;
use Magento\Data\Collection;
use Magento\Data\Tree\Node;
use Magento\Event\Observer as EventObserver;
use Magento\Sales\Model\Quote;

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
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * Catalog event data
     *
     * @var Data
     */
    protected $_catalogEventData;

    /**
     * Store manager model
     *
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Event collection factory
     *
     * @var CollectionFactory
     */
    protected $_eventCollectionFactory;

    /**
     * Event model factory
     *
     * @var EventFactory
     */
    protected $_eventFactory;

    /**
     * Construct
     * 
     * @param Data $catalogEventData
     * @param Registry $coreRegistry
     * @param StoreManagerInterface $storeManager
     * @param CollectionFactory $eventCollectionFactory
     * @param EventFactory $eventFactory
     */
    public function __construct(
        Data $catalogEventData,
        Registry $coreRegistry,
        StoreManagerInterface $storeManager,
        CollectionFactory $eventCollectionFactory,
        EventFactory $eventFactory
    ) {
        $this->_catalogEventData = $catalogEventData;
        $this->_coreRegistry = $coreRegistry;
        $this->_storeManager = $storeManager;
        $this->_eventFactory = $eventFactory;
        $this->_eventCollectionFactory = $eventCollectionFactory;
    }

    /**
     * Applies event to category
     *
     * @param EventObserver $observer
     * @return void|$this
     */
    public function applyEventToCategory(EventObserver $observer)
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
     * @param EventObserver $observer
     * @return void|$this
     */
    public function applyEventToCategoryCollection(EventObserver $observer)
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
     * @param EventObserver $observer
     * @return void|$this
     */
    public function applyEventToProduct(EventObserver $observer)
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
     * @param EventObserver $observer
     * @return $this
     */
    public function applyIsSalableToProduct(EventObserver $observer)
    {
        $event = $observer->getEvent()->getProduct()->getEvent();
        if ($event && in_array($event->getStatus(), array(
            Event::STATUS_CLOSED,
            Event::STATUS_UPCOMING
        ))) {
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
     * @param EventObserver $observer
     * @return void|$this
     */
    public function applyEventOnQuoteItemSetProduct(EventObserver $observer)
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
     * @param EventObserver $observer
     * @return void|$this
     */
    public function applyEventOnQuoteItemSetQty(EventObserver $observer)
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
                if ($event->getStatus() !== Event::STATUS_OPEN) {
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
     * @param EventObserver $observer
     * @return void|$this
     */
    public function applyEventToProductCollection(EventObserver $observer)
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
     * @param Product $product
     * @return Event|false
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
     * @return Event|false
     */
    protected function _getEventInStore($categoryId)
    {
        if ($this->_coreRegistry->registry('current_category')
            && $this->_coreRegistry->registry('current_category')->getId() == $categoryId) {
            // If category already loaded for page, we don't need to load categories tree
            return $this->_coreRegistry->registry('current_category')->getEvent();
        }

        if ($this->_eventsToCategories === null) {
            $this->_eventsToCategories = $this->_eventFactory->create()->getCategoryIdsWithEvent(
                $this->_storeManager->getStore()->getId()
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
     * @param string[] $categoryIds
     * @return EventCollection
     */
    protected function _getEventCollection(array $categoryIds = null)
    {
        /** @var EventCollection $collection */
        $collection = $this->_eventCollectionFactory->create();
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
     * @param Quote $quote
     * @return $this
     */
    protected function _initializeEventsForQuoteItems(Quote $quote)
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
