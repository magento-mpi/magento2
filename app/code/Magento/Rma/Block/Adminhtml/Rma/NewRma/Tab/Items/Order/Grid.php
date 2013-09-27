<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin RMA create order grid block
 */
namespace Magento\Rma\Block\Adminhtml\Rma\NewRma\Tab\Items\Order;

class Grid
    extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Variable to store store-depended string values of attributes
     *
     * @var null|array
     */
    protected $_attributeOptionValues = null;

    /**
     * Default limit for order item collection
     *
     * We cannot manage items quantity in right way so we get all the items without limits and paging
     *
     * @var int
     */
    protected $_defaultLimit = 0;

    /**
     * Rma data
     *
     * @var \Magento\Rma\Helper\Data
     */
    protected $_rmaData;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Rma\Model\Resource\ItemFactory
     */
    protected $_itemFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @param \Magento\Rma\Model\Resource\ItemFactory $itemFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Rma\Helper\Data $rmaData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Url $urlModel
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Rma\Model\Resource\ItemFactory $itemFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Rma\Helper\Data $rmaData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Url $urlModel,
        \Magento\Core\Model\Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_itemFactory = $itemFactory;
        $this->_productFactory = $productFactory;
        $this->_rmaData = $rmaData;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
    }

    /**
     * Block constructor
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('order_items_grid');
        $this->setDefaultSort('item_id');
        $this->setUseAjax(true);
        $this->setPagerVisibility(false);
    }

    /**
     * Prepare grid collection object
     *
     * @return \Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\Items\Grid
     */
    protected function _prepareCollection()
    {
        $orderId = $this->_coreRegistry->registry('current_order')->getId();
        /** @var $resourceItem \Magento\Rma\Model\Resource\Item */
        $resourceItem = $this->_itemFactory->create();
        $orderItemsCollection = $resourceItem->getOrderItemsCollection($orderId);
        $this->setCollection($orderItemsCollection);
        return parent::_prepareCollection();
    }

    /**
     * After load collection processing.
     *
     * Filter items collection due to RMA needs. Remove forbidden items, non-applicable
     * bundles (and their children) and configurables
     *
     * @return \Magento\Rma\Block\Adminhtml\Rma\NewRma\Tab\Items\Order_Grid
     */
    protected function _afterLoadCollection()
    {
        $orderId = $this->_coreRegistry->registry('current_order')->getId();
        /** @var $resourceItem \Magento\Rma\Model\Resource\Item */
        $resourceItem = $this->_itemFactory->create();
        $itemsInActiveRmaArray = $resourceItem->getItemsIdsByOrder($orderId);

        /** @var $resourceItem \Magento\Rma\Model\Resource\Item */
        $resourceItem = $this->_itemFactory->create();
        $fullItemsCollection = $resourceItem->getOrderItemsCollection($orderId);
        /**
         * contains data that defines possibility of return for an order item
         * array value ['self'] refers to item's own rules
         * array value ['child'] refers to rules defined from item's sub-items
         */
        $parent = array();

        /** @var $product \Magento\Catalog\Model\Product */
        $product = $this->_productFactory->create();

        foreach ($fullItemsCollection as $item) {
            $allowed = true;
            if (in_array($item->getId(), $itemsInActiveRmaArray)) {
                $allowed = false;
            }

            if ($allowed === true) {
                $product->reset();
                $product->setStoreId($item->getStoreId());
                $product->load($item->getProductId());

                if (!$this->_rmaData->canReturnProduct($product, $item->getStoreId())) {
                    $allowed = false;
                }
            }

            if ($item->getParentItemId()) {
                if (!isset($parent[$item->getParentItemId()]['child'])) {
                    $parent[$item->getParentItemId()]['child'] = false;
                }
                $parent[$item->getParentItemId()]['child']  = $parent[$item->getParentItemId()]['child'] || $allowed;
                $parent[$item->getItemId()]['self']         = false;
            } else {
                $parent[$item->getItemId()]['self']         = $allowed;
            }
        }

        foreach ($this->getCollection() as $item) {
            if (isset($parent[$item->getId()]['self']) && $parent[$item->getId()]['self'] === false) {
                $this->getCollection()->removeItemByKey($item->getId());
                continue;
            }
            if (isset($parent[$item->getId()]['child']) && $parent[$item->getId()]['child'] === false) {
                $this->getCollection()->removeItemByKey($item->getId());
                continue;
            }
            if ($item->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE
                && !isset($parent[$item->getId()]['child'])
            ) {
                $this->getCollection()->removeItemByKey($item->getId());
                continue;
            }

            if ($item->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_CONFIGURABLE) {
                $productOptions     = $item->getProductOptions();
                $product->reset();
                $product->load($product->getIdBySku($productOptions['simple_sku']));
                if (!$this->_rmaData->canReturnProduct($product, $item->getStoreId())) {
                    $this->getCollection()->removeItemByKey($item->getId());
                    continue;
                }
            }

            $item->setName($this->_rmaData->getAdminProductName($item));
        }

        return $this;
    }

    /**
     * Prepare columns
     *
     * @return \Magento\Adminhtml\Block\Widget\Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('select', array(
            'header'   => __('Select'),
            'type'     => 'checkbox',
            'align'    => 'center',
            'sortable' => false,
            'index'    => 'item_id',
            'values'   => $this->_getSelectedProducts(),
            'name'     => 'in_products',
            'header_css_class'  => 'col-select',
            'column_css_class'  => 'col-select'
        ));

        $this->addColumn('product_name', array(
            'header'   => __('Product'),
            'renderer' => 'Magento\Rma\Block\Adminhtml\Product\Bundle\Product',
            'index'    => 'name',
            'escape'   => true,
            'header_css_class'  => 'col-product',
            'column_css_class'  => 'col-product'
        ));

        $this->addColumn('sku', array(
            'header' => __('SKU'),
            'type'   => 'text',
            'index'  => 'sku',
            'escape' => true,
            'header_css_class'  => 'col-sku',
            'column_css_class'  => 'col-sku'
        ));

        $this->addColumn('price', array(
            'header'=> __('Price'),
            'type'  => 'currency',
            'index' => 'price',
            'header_css_class'  => 'col-price',
            'column_css_class'  => 'col-price'
        ));

        $this->addColumn('available_qty', array(
            'header'=> __('Remaining'),
            'type'  => 'text',
            'index' => 'available_qty',
            'renderer'  => 'Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Quantity',
            'filter' => false,
            'sortable' => false,
            'header_css_class'  => 'col-qty',
            'column_css_class'  => 'col-qty'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        $js = '
            function (grid, event) {
                return rma.addProductRowCallback(grid, event);
            }
        ';
        return $js;
    }

    /**
     * Checkbox Click JS Callback
     *
     * @return string
     */
    public function getCheckboxCheckCallback()
    {
        $js = '
            function (grid, element, checked) {
                return rma.addProductCheckboxCheckCallback(grid, element, checked);
            }
        ';
        return $js;
    }

    /**
     * Get Url to action to reload grid
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/addProductGrid', array('_current' => true));
    }

    /**
     * List of selected products
     *
     * @return array
     */
    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('products', array());

        if (!is_array($products)) {
            $products = array();
        } else {
            foreach ($products as &$value) {
                $value = intval($value);
            }
        }

        return $products;
    }

    /**
     * Setting column filters to collection
     *
     * @param \Magento\Adminhtml\Block\Widget\Grid\Column $column
     * @return \Magento\Rma\Block\Adminhtml\Rma\NewRma\Tab\Items\Order_Grid
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for selected products flag
        if ($column->getId() == 'select') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('item_id', array('in'=>$productIds));
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('item_id', array('nin'=>$productIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
}
