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
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Rma\Block\Adminhtml\Rma\NewRma\Tab\Items\Order;

class Grid
    extends \Magento\Adminhtml\Block\Widget\Grid
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
        $orderId = \Mage::registry('current_order')->getId();

        /** @var $collection \Magento\Rma\Model\Resource\Item */

        $orderItemsCollection = \Mage::getResourceModel('Magento\Rma\Model\Resource\Item')
            ->getOrderItemsCollection($orderId);

        $this->setCollection($orderItemsCollection);

        return parent::_prepareCollection();
    }

    /**
     * After load collection processing.
     *
     * Filter items collection due to RMA needs. Remove forbidden items, non-applicable
     * bundles (and their children) and configurables
     *
     * @return \Magento\Rma\Block\Adminhtml\Rma\NewRma\Tab\Items\Order\Grid
     */
    protected function _afterLoadCollection()
    {
        $orderId = \Mage::registry('current_order')->getId();
        $itemsInActiveRmaArray = \Mage::getResourceModel('Magento\Rma\Model\Resource\Item')
            ->getItemsIdsByOrder($orderId);

        $fullItemsCollection = \Mage::getResourceModel('Magento\Rma\Model\Resource\Item')
            ->getOrderItemsCollection($orderId);
        /**
         * contains data that defines possibility of return for an order item
         * array value ['self'] refers to item's own rules
         * array value ['child'] refers to rules defined from item's sub-items
         */
        $parent = array();

        /** @var $product \Magento\Catalog\Model\Product */
        $product = \Mage::getModel('Magento\Catalog\Model\Product');

        foreach ($fullItemsCollection as $item) {
            $allowed = true;
            if (in_array($item->getId(), $itemsInActiveRmaArray)) {
                $allowed = false;
            }

            if ($allowed === true) {
                $product->reset();
                $product->setStoreId($item->getStoreId());
                $product->load($item->getProductId());

                if (!\Mage::helper('Magento\Rma\Helper\Data')->canReturnProduct($product, $item->getStoreId())) {
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
                if (!\Mage::helper('Magento\Rma\Helper\Data')->canReturnProduct($product, $item->getStoreId())) {
                    $this->getCollection()->removeItemByKey($item->getId());
                    continue;
                }
            }

            $item->setName(\Mage::helper('Magento\Rma\Helper\Data')->getAdminProductName($item));
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
            'renderer' => '\Magento\Rma\Block\Adminhtml\Product\Bundle\Product',
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
            'renderer'  => '\Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\Items\Grid\Column\Renderer\Quantity',
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
     * @return \Magento\Rma\Block\Adminhtml\Rma\NewRma\Tab\Items\Order\Grid
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
                if($productIds) {
                    $this->getCollection()->addFieldToFilter('item_id', array('nin'=>$productIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
}
