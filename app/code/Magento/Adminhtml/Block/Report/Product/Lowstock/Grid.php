<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml low stock products report grid block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Report_Product_Lowstock_Grid extends Magento_Adminhtml_Block_Widget_Grid
{
//    protected $_saveParametersInSession = true;

    protected function _construct()
    {
        parent::_construct();
        $this->setId('gridLowstock');
        $this->setUseAjax(false);
    }

    protected function _prepareCollection()
    {
        if ($this->getRequest()->getParam('website')) {
            $storeIds = Mage::app()->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
            $storeId = array_pop($storeIds);
        } else if ($this->getRequest()->getParam('group')) {
            $storeIds = Mage::app()->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
            $storeId = array_pop($storeIds);
        } else if ($this->getRequest()->getParam('store')) {
            $storeId = (int)$this->getRequest()->getParam('store');
        } else {
            $storeId = '';
        }

        /** @var $collection Magento_Reports_Model_Resource_Product_Lowstock_Collection  */
        $collection = Mage::getResourceModel('Magento_Reports_Model_Resource_Product_Lowstock_Collection')
            ->addAttributeToSelect('*')
            ->setStoreId($storeId)
            ->filterByIsQtyProductTypes()
            ->joinInventoryItem('qty')
            ->useManageStockFilter($storeId)
            ->useNotifyStockQtyFilter($storeId)
            ->setOrder('qty', Magento_Data_Collection::SORT_ORDER_ASC);

        if( $storeId ) {
            $collection->addStoreFilter($storeId);
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'    =>Mage::helper('Magento_Reports_Helper_Data')->__('Product'),
            'sortable'  =>false,
            'index'     =>'name',
            'header_css_class'  => 'col-product',
            'column_css_class'  => 'col-product'
        ));

        $this->addColumn('sku', array(
            'header'    =>Mage::helper('Magento_Reports_Helper_Data')->__('SKU'),
            'sortable'  =>false,
            'index'     =>'sku',
            'header_css_class'  => 'col-sku',
            'column_css_class'  => 'col-sku'
        ));

        $this->addColumn('qty', array(
            'header'    =>Mage::helper('Magento_Reports_Helper_Data')->__('Stock Quantity'),
            'sortable'  =>false,
            'filter'    =>'Magento_Adminhtml_Block_Widget_Grid_Column_Filter_Range',
            'index'     =>'qty',
            'type'      =>'number',
            'header_css_class'  => 'col-qty',
            'column_css_class'  => 'col-qty'
        ));

        $this->addExportType('*/*/exportLowstockCsv', Mage::helper('Magento_Reports_Helper_Data')->__('CSV'));
        $this->addExportType('*/*/exportLowstockExcel', Mage::helper('Magento_Reports_Helper_Data')->__('Excel XML'));

        return parent::_prepareColumns();
    }
}
