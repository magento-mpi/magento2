<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin RMA create order grid block
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_Rma_Block_Adminhtml_Rma_New_Tab_Items_Order_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
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
     * @return Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid
     */
    protected function _prepareCollection()
    {
        $orderId = Mage::registry('current_order')->getId();

        /** @var $collection Enterprise_Rma_Model_Resource_Item */

        $orderItemsCollection = Mage::getResourceModel('Enterprise_Rma_Model_Resource_Item')
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
     * @return Enterprise_Rma_Block_Adminhtml_Rma_New_Tab_Items_Order_Grid
     */
    protected function _afterLoadCollection()
    {
        $orderId = Mage::registry('current_order')->getId();
        $itemsInActiveRmaArray = Mage::getResourceModel('Enterprise_Rma_Model_Resource_Item')
            ->getItemsIdsByOrder($orderId);

        $fullItemsCollection = Mage::getResourceModel('Enterprise_Rma_Model_Resource_Item')
            ->getOrderItemsCollection($orderId);
        /**
         * contains data that defines possibility of return for an order item
         * array value ['self'] refers to item's own rules
         * array value ['child'] refers to rules defined from item's sub-items
         */
        $parent = array();

        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('Mage_Catalog_Model_Product');

        foreach ($fullItemsCollection as $item) {
            $allowed = true;
            if (in_array($item->getId(), $itemsInActiveRmaArray)) {
                $allowed = false;
            }

            if ($allowed === true) {
                $product->reset();
                $product->setStoreId($item->getStoreId());
                $product->load($item->getProductId());

                if (!Mage::helper('Enterprise_Rma_Helper_Data')->canReturnProduct($product, $item->getStoreId())) {
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
            if ($item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE
                && !isset($parent[$item->getId()]['child'])
            ) {
                $this->getCollection()->removeItemByKey($item->getId());
                continue;
            }

            if ($item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                $productOptions     = $item->getProductOptions();
                $product->reset();
                $product->load($product->getIdBySku($productOptions['simple_sku']));
                if (!Mage::helper('Enterprise_Rma_Helper_Data')->canReturnProduct($product, $item->getStoreId())) {
                    $this->getCollection()->removeItemByKey($item->getId());
                    continue;
                }
            }

            $item->setName(Mage::helper('Enterprise_Rma_Helper_Data')->getAdminProductName($item));
        }

        return $this;
    }

    /**
     * Prepare columns
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('select', array(
            'header'=> Mage::helper('Enterprise_Rma_Helper_Data')->__('Select'),
            'width' => '40px',
            'type'  => 'checkbox',
            'align'     => 'center',
            'sortable' => false,
            'index' => 'item_id',
            'values'    => $this->_getSelectedProducts(),
            'name'      => 'in_products',
        ));

        $this->addColumn('product_name', array(
            'header'    => Mage::helper('Enterprise_Rma_Helper_Data')->__('Product Name'),
            'renderer'  => 'Enterprise_Rma_Block_Adminhtml_Product_Bundle_Product',
            'index'     => 'name'
        ));

        $this->addColumn('sku', array(
            'header'=> Mage::helper('Enterprise_Rma_Helper_Data')->__('SKU'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'sku',
        ));

        $this->addColumn('price', array(
            'header'=> Mage::helper('Enterprise_Rma_Helper_Data')->__('Price'),
            'width' => '80px',
            'type'  => 'currency',
            'index' => 'price',
        ));

        $this->addColumn('available_qty', array(
            'header'=> Mage::helper('Enterprise_Rma_Helper_Data')->__('Remaining Qty'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'available_qty',
            'renderer'  => 'Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Quantity',
            'filter' => false,
            'sortable' => false,
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
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return Enterprise_Rma_Block_Adminhtml_Rma_New_Tab_Items_Order_Grid
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
