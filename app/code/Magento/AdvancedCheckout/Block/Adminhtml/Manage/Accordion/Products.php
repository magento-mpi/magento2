<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\AdvancedCheckout\Block\Adminhtml\Manage\Accordion;

use Magento\Backend\Block\Widget\Grid\Column;

/**
 * Accordion grid for catalog salable products
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Products extends AbstractAccordion
{
    /**
     * @var \Magento\Sales\Model\Config
     */
    protected $_salesConfig;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Catalog\Model\Config
     */
    protected $_catalogConfig;

    /**
     * @var \Magento\CatalogInventory\Helper\Stock
     */
    protected $stockHelper;

    /**
     * @var \Magento\Framework\Json\DecoderInterface
     */
    protected $_jsonDecoder;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Data\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Json\DecoderInterface $jsonDecoder
     * @param \Magento\CatalogInventory\Helper\Stock $stockHelper
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\Sales\Model\Config $salesConfig
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Data\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Json\DecoderInterface $jsonDecoder,
        \Magento\CatalogInventory\Helper\Stock $stockHelper,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Sales\Model\Config $salesConfig,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        array $data = []
    ) {
        $this->_jsonDecoder = $jsonDecoder;
        parent::__construct($context, $backendHelper, $collectionFactory, $coreRegistry, $data);
        $this->stockHelper = $stockHelper;
        $this->_catalogConfig = $catalogConfig;
        $this->_salesConfig = $salesConfig;
        $this->_productFactory = $productFactory;
    }

    /**
     * Block initializing, grid parameters
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('source_products');
        $this->setDefaultSort('entity_id');
        $this->setPagerVisibility(true);
        $this->setFilterVisibility(true);
        $this->setHeaderText(__('Products'));
    }

    /**
     * Return custom object name for js grid
     *
     * @return string
     */
    public function getJsObjectName()
    {
        return 'productsGrid';
    }

    /**
     * Return items collection
     *
     * @return \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
     */
    public function getItemsCollection()
    {
        if (!$this->hasData('items_collection')) {
            $attributes = $this->_catalogConfig->getProductAttributes();
            $collection = $this->_productFactory->create()->getCollection()->setStore(
                $this->_getStore()
            )->addAttributeToSelect(
                $attributes
            )->addAttributeToSelect(
                'sku'
            )->addAttributeToFilter(
                'type_id',
                $this->_salesConfig->getAvailableProductTypes()
            )->addAttributeToFilter(
                'status',
                \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
            )->addStoreFilter(
                $this->_getStore()
            );
            $this->stockHelper->addIsInStockFilterToCollection($collection);
            $this->setData('items_collection', $collection);
        }
        return $this->getData('items_collection');
    }

    /**
     * Prepare Grid columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            ['header' => __('ID'), 'sortable' => true, 'width' => '60', 'index' => 'entity_id']
        );

        $this->addColumn(
            'name',
            [
                'header' => __('Product'),
                'renderer' => 'Magento\Sales\Block\Adminhtml\Order\Create\Search\Grid\Renderer\Product',
                'index' => 'name'
            ]
        );

        $this->addColumn('sku', ['header' => __('SKU'), 'width' => '80', 'index' => 'sku']);

        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'type' => 'currency',
                'column_css_class' => 'price',
                'currency_code' => $this->_getStore()->getCurrentCurrencyCode(),
                'rate' => $this->_getStore()->getBaseCurrency()->getRate($this->_getStore()->getCurrentCurrencyCode()),
                'index' => 'price',
                'renderer' => 'Magento\Sales\Block\Adminhtml\Order\Create\Search\Grid\Renderer\Price'
            ]
        );

        $this->_addControlColumns();

        return $this;
    }

    /**
     * Custom products grid search callback
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->getChildBlock('search_button')->setOnclick('checkoutObj.searchProducts()');
        return $this;
    }

    /**
     * Search by selected products
     *
     * @param Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (!$productIds) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            } elseif ($productIds) {
                $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Return array of selected product ids from request
     *
     * @return array|false
     */
    protected function _getSelectedProducts()
    {
        if ($this->getRequest()->getPost('source')) {
            $source = $this->_jsonDecoder->decode($this->getRequest()->getPost('source'));
            if (isset($source['source_products']) && is_array($source['source_products'])) {
                return array_keys($source['source_products']);
            }
        }
        return false;
    }

    /**
     * Return grid URL for sorting and filtering
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('checkout/*/products', ['_current' => true]);
    }

    /**
     * Add columns with controls to manage added products and their quantity
     *
     * @return void
     */
    protected function _addControlColumns()
    {
        parent::_addControlColumns();
        $this->getColumn('in_products')->setHeader(" ");
    }

    /**
     * Add custom options to product collection
     *
     * @return $this
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->addOptionsToResult();
        return parent::_afterLoadCollection();
    }
}
