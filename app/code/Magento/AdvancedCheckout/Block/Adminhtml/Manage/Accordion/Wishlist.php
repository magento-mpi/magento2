<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\AdvancedCheckout\Block\Adminhtml\Manage\Accordion;

/**
 * Accordion grid for products in wishlist
 */
class Wishlist extends AbstractAccordion
{
    /**
     * Collection field name for using in controls
     *
     * @var string
     */
    protected $_controlFieldName = 'wishlist_item_id';

    /**
     * Javascript list type name for this grid
     *
     * @var string
     */
    protected $_listType = 'wishlist';

    /**
     * Url to configure this grid's items
     *
     * @var string
     */
    protected $_configureRoute = '*/checkout/configureWishlistItem';

    /**
     * Wishlist item collection factory
     *
     * @var \Magento\Wishlist\Model\Resource\Item\CollectionFactory
     */
    protected $_itemFactory;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Data\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Wishlist\Model\Resource\Item\CollectionFactory $itemFactory
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Data\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Wishlist\Model\Resource\Item\CollectionFactory $itemFactory,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        array $data = []
    ) {
        $this->_itemFactory = $itemFactory;
        $this->stockRegistry = $stockRegistry;
        parent::__construct($context, $backendHelper, $collectionFactory, $coreRegistry, $data);
    }

    /**
     * Initialize Grid
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('source_wishlist');
        $this->setDefaultSort('added_at');
        $this->setData('open', true);
        if ($this->_getStore()) {
            $this->setHeaderText(__('Wish List (%1)', $this->getItemsCount()));
        }
    }

    /**
     * Return custom object name for js grid
     *
     * @return string
     */
    public function getJsObjectName()
    {
        return 'wishlistItemsGrid';
    }

    /**
     * Create wishlist item collection
     *
     * @return \Magento\Wishlist\Model\Resource\Item\Collection
     */
    protected function _createItemsCollection()
    {
        return $this->_itemFactory->create();
    }

    /**
     * Return items collection
     *
     * @return \Magento\Wishlist\Model\Resource\Item\Collection
     */
    public function getItemsCollection()
    {
        if (!$this->hasData('items_collection')) {
            $collection = $this->_createItemsCollection()
                ->addCustomerIdFilter($this->_getCustomer()->getId())
                ->addStoreFilter($this->_getStore()->getWebsite()->getStoreIds())
                ->setVisibilityFilter()
                ->setSalableFilter()
                ->resetSortOrder();

            foreach ($collection as $item) {
                $product = $item->getProduct();
                if ($product) {
                    $stockItem = $this->stockRegistry->getStockItem(
                        $product->getId(),
                        $this->_getStore()->getWebsiteId()
                    );
                    if (!$stockItem->getIsInStock() || !$product->isInStock()) {
                        // Remove disabled and out of stock products from the grid
                        $collection->removeItemByKey($item->getId());
                    } else {
                        $item->setName($product->getName());
                        $item->setPrice($product->getPrice());
                    }
                }
            }
            $this->setData('items_collection', $collection);
        }
        return $this->_getData('items_collection');
    }

    /**
     * Return grid URL for sorting and filtering
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('checkout/*/viewWishlist', ['_current' => true]);
    }

    /**
     * Add columns with controls to manage added products and their quantity
     * Uses inherited methods, but modifies Qty column to change renderer
     *
     * @return $this
     */
    protected function _addControlColumns()
    {
        parent::_addControlColumns();
        $this->getColumn('qty')->addData(
            ['renderer' => 'Magento\AdvancedCheckout\Block\Adminhtml\Manage\Grid\Renderer\Wishlist\Qty']
        );

        return $this;
    }
}
