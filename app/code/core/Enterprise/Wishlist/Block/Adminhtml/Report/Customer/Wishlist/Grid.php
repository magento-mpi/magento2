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
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid Id
     */
    public function __construct()
    {
        parent::__construct();
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
            'header'    => Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Added at'),
            'align'     =>'right',
            'width'     => 100,
            'index'     => 'added_at',
            'type'      => 'datetime'
        ));
        $this->addColumn('customer_name', array(
            'header'    => Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Customer Name'),
            'align'     =>'right',
            'width'     => 250,
            'index'     => 'customer_name',
            'filter'    => false
        ));

        $this->addColumn('wishlist_name', array(
            'header'    => Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Wishlist Name'),
            'align'     =>'right',
            'width'     => 150,
            'index'     => 'wishlist_name',
        ));

        $this->addColumn('visibility', array(
            'header'    => Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Wishlist BugsCoverage'),
            'align'     => 'left',
            'index'     => 'visibility',
            'type'      => 'options',
            'width'     => 100,
            'options'   => array(
                1 => Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Public'),
                0 => Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Private'),
            ),
        ));

        $this->addColumn('prouduct_name', array(
            'header'    => Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Product Name'),
            'align'     => 'right',
            'width'     => 250,
            'index'     => 'product_name',
            'sortable'  => false,
            'filter'    => false
        ));

        $this->addColumn('prouduct_sku', array(
            'header'    => Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Product SKU'),
            'align'     => 'right',
            'width'     => 100,
            'index'     => 'product_sku',
            'filter'    => false,
            'sortable'  => false
        ));

        $this->addColumn('item_comment', array(
            'header'    => Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Comment'),
            'align'     => 'right',
            'width'     => 350,
            'index'     => 'description',
        ));

        $this->addColumn('item_qty', array(
            'header'    => Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Qty in wishlist'),
            'align'     => 'right',
            'width'     => 80,
            'index'     => 'item_qty',
            'type'      => 'number'
        ));

        if (Mage::helper('Mage_Catalog_Helper_Data')->isModuleEnabled('Mage_CatalogInventory')) {
            $this->addColumn('product_qty', array(
                'header'    => Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Qty available in store'),
                'align'     => 'right',
                'width'     => 80,
                'index'     => 'product_qty',
                'type'      => 'number'
            ));

            $this->addColumn('qty_diff', array(
                'header'    => Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Difference between qtys'),
                'align'     => 'right',
                'width'     => 80,
                'index'     => 'qty_diff',
                'type'      => 'number'
            ));
        }
        $storeIds = $this->_getStoreIds();
        $store = Mage::app()->getStore((int) $storeIds[0]);

        $this->addColumn('product_price', array(
            'header'    => Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Price'),
            'align'     => 'right',
            'width'     => 80,
            'index'     => 'product_price',
            'sortable'  => false,
            'filter'    => false,
            'type'      => 'price',
            'currency_code' => $store->getBaseCurrency()->getCode()

        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('Mage_Adminhtml_Helper_Data')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('Mage_Adminhtml_Helper_Data')->__('Excel XML'));

        return $this;
    }
}
