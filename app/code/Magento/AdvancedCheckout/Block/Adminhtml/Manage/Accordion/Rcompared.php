<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdvancedCheckout\Block\Adminhtml\Manage\Accordion;

/**
 * Accordion grid for Recently compared products
 */
class Rcompared extends AbstractAccordion
{
    /**
     * Javascript list type name for this grid
     *
     * @var string
     */
    protected $_listType = 'rcompared';

    /**
     * @var \Magento\Sales\Helper\Admin
     */
    protected $_adminhtmlSales;

    /**
     * @var \Magento\Catalog\Model\Resource\Product\Compare\Item\CollectionFactory
     */
    protected $_compareListFactory;

    /**
     * @var \Magento\Catalog\Model\Resource\Product\CollectionFactory
     */
    protected $productListFactory;

    /**
     * @var \Magento\Catalog\Model\Config
     */
    protected $_catalogConfig;

    /**
     * @var \Magento\Reports\Model\Resource\Event
     */
    protected $_reportsEventResource;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Data\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\Reports\Model\Resource\Event $reportsEventResource
     * @param \Magento\Sales\Helper\Admin $adminhtmlSales
     * @param \Magento\Catalog\Model\Resource\Product\CollectionFactory $productListFactory
     * @param \Magento\Catalog\Model\Resource\Product\Compare\Item\CollectionFactory $compareListFactory
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Data\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Reports\Model\Resource\Event $reportsEventResource,
        \Magento\Sales\Helper\Admin $adminhtmlSales,
        \Magento\Catalog\Model\Resource\Product\CollectionFactory $productListFactory,
        \Magento\Catalog\Model\Resource\Product\Compare\Item\CollectionFactory $compareListFactory,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        array $data = array()
    ) {
        $this->_catalogConfig = $catalogConfig;
        $this->_reportsEventResource = $reportsEventResource;
        $this->_adminhtmlSales = $adminhtmlSales;
        $this->productListFactory = $productListFactory;
        $this->_compareListFactory = $compareListFactory;
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
        $this->setId('source_rcompared');
        if ($this->_getStore()) {
            $this->setHeaderText(__('Recently Compared Products (%1)', $this->getItemsCount()));
        }
    }

    /**
     * Return items collection
     *
     * @return \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
     */
    public function getItemsCollection()
    {
        if (!$this->hasData('items_collection')) {
            $skipProducts = array();
            $collection = $this->_compareListFactory->create();
            $collection->useProductItem(true)
                ->setStoreId($this->_getStore()->getId())
                ->addStoreFilter($this->_getStore()->getId())
                ->setCustomerId($this->_getCustomer()->getId());

            foreach ($collection as $item) {
                $skipProducts[] = $item->getProductId();
            }

            // prepare products collection and apply visitors log to it
            $attributes = $this->_catalogConfig->getProductAttributes();
            if (!in_array('status', $attributes)) {
                // Status attribute is required even if it is not used in product listings
                $attributes[] = 'status';
            }
            $productCollection = $this->productListFactory->create()
                ->setStoreId($this->_getStore()->getId())
                ->addStoreFilter($this->_getStore()->getId())
                ->addAttributeToSelect($attributes);

            $this->_reportsEventResource->applyLogToCollection(
                $productCollection,
                \Magento\Reports\Model\Event::EVENT_PRODUCT_COMPARE,
                $this->_getCustomer()->getId(),
                0,
                $skipProducts
            );
            $productCollection = $this->_adminhtmlSales->applySalableProductTypesFilter($productCollection);
            // Remove disabled and out of stock products from the grid
            foreach ($productCollection as $product) {
                $stockItem = $this->stockRegistry->getStockItem($product->getId(), $this->_getStore()->getWebsiteId());
                if (!$stockItem->getIsInStock() || !$product->isInStock()) {
                    $productCollection->removeItemByKey($product->getId());
                }
            }
            $productCollection->addOptionsToResult();
            $this->setData('items_collection', $productCollection);
        }
        return $this->_getData('items_collection');
    }

    /**
     * Retrieve Grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('checkout/*/viewRecentlyCompared', array('_current' => true));
    }
}
