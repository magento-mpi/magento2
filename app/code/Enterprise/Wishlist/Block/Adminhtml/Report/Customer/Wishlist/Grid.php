<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer wishlist item grid
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Wishlist_Block_Adminhtml_Report_Customer_Wishlist_Grid
    extends Magento_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid Id
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('gridReportWishlists');
    }

    /**
     * Prepare wishlist item collection
     *
     * @return Enterprise_Wishlist_Block_Adminhtml_Report_Customer_Wishlist_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('Enterprise_Wishlist_Model_Resource_Item_Report_Collection');
        $collection->filterByStoreIds($this->_getStoreIds());
        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;
    }

    /**
     * Get allowed store ids array intersected with selected scope in store switcher
     *
     * @return  array
     */
    protected function _getStoreIds()
    {
        $storeIdsStr = $this->getRequest()->getParam('store_ids');
        $allowedStoreIds = array_keys(Mage::app()->getStores());
        $storeIds = array();
        if (strlen($storeIdsStr)) {
            $storeIds = explode(',', $storeIdsStr);
            $storeIds = array_intersect($allowedStoreIds, $storeIds);
        } else {
            $storeIds = $allowedStoreIds;
        }
        $storeIds = array_values($storeIds);
        return $storeIds;
    }

    /**
     * Add grid columns
     *
     * @return Enterprise_Wishlist_Block_Adminhtml_Report_Customer_Wishlist_Grid
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumn('added_at', array(
            'header'    => Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Added'),
            'index'     => 'added_at',
            'type'      => 'datetime',
            'header_css_class'  => 'col-period',
            'column_css_class'  => 'col-period'
        ));
        $this->addColumn('customer_name', array(
            'header'    => Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Customer'),
            'index'     => 'customer_name',
            'filter'    => false,
            'header_css_class'  => 'col-name',
            'column_css_class'  => 'col-name',
        ));

        $this->addColumn('wishlist_name', array(
            'header'    => Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Wishlist'),
            'index'     => 'wishlist_name',
            'header_css_class'  => 'col-whishlist-name',
            'column_css_class'  => 'col-whishlist-name'
        ));

        $this->addColumn('visibility', array(
            'header'    => Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Status'),
            'index'     => 'visibility',
            'type'      => 'options',
            'options'   => array(
                1 => Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Public'),
                0 => Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Private'),
            ),
            'header_css_class'  => 'col-whishlist-status',
            'column_css_class'  => 'col-whishlist-status'
        ));

        $this->addColumn('prouduct_name', array(
            'header'    => Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Product'),
            'index'     => 'product_name',
            'sortable'  => false,
            'filter'    => false,
            'header_css_class'  => 'col-product',
            'column_css_class'  => 'col-product'
        ));

        $this->addColumn('prouduct_sku', array(
            'header'    => Mage::helper('Enterprise_Wishlist_Helper_Data')->__('SKU'),
            'index'     => 'product_sku',
            'filter'    => false,
            'sortable'  => false,
            'header_css_class'  => 'col-sku',
            'column_css_class'  => 'col-sku'
        ));

        $this->addColumn('item_comment', array(
            'header'    => Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Comment'),
            'index'     => 'description',
            'header_css_class'  => 'col-comment',
            'column_css_class'  => 'col-comment'
        ));

        $this->addColumn('item_qty', array(
            'header'    => Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Wishlist Quantity'),
            'index'     => 'item_qty',
            'type'      => 'number',
            'header_css_class'  => 'col-whishlist-qty',
            'column_css_class'  => 'col-whishlist-qty'
        ));

        if (Mage::helper('Mage_Catalog_Helper_Data')->isModuleEnabled('Mage_CatalogInventory')) {
            $this->addColumn('product_qty', array(
                'header'    => Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Store Quantity'),
                'index'     => 'product_qty',
                'type'      => 'number',
                'header_css_class'  => 'col-available-qty',
                'column_css_class'  => 'col-available-qty'
            ));

            $this->addColumn('qty_diff', array(
                'header'    => Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Surplus / Deficit'),
                'index'     => 'qty_diff',
                'type'      => 'number',
                'header_css_class'  => 'col-qty-diff',
                'column_css_class'  => 'col-qty-diff'
            ));
        }
        $storeIds = $this->_getStoreIds();
        $store = Mage::app()->getStore((int) $storeIds[0]);

        $this->addColumn('product_price', array(
            'header'            => Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Price'),
            'index'             => 'product_price',
            'sortable'          => false,
            'filter'            => false,
            'type'              => 'price',
            'currency_code'     => $store->getBaseCurrency()->getCode(),
            'header_css_class'  => 'col-price',
            'column_css_class'  => 'col-price'
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('Magento_Adminhtml_Helper_Data')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('Magento_Adminhtml_Helper_Data')->__('Excel XML'));

        return $this;
    }
}
