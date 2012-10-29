<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml customers by totals report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Customer_Totals_Grid extends Mage_Adminhtml_Block_Report_Grid
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('gridTotalsCustomer');
    }

    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->getCollection()->initReport('Mage_Reports_Model_Resource_Customer_Totals_Collection');
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'    => $this->__('Customer Name'),
            'sortable'  => false,
            'index'     => 'name'
        ));

        $this->addColumn('orders_count', array(
            'header'    => $this->__('Number of Orders'),
            'width'     => '100px',
            'sortable'  => false,
            'index'     => 'orders_count',
            'total'     => 'sum',
            'type'      => 'number'
        ));

        $baseCurrencyCode = $this->getCurrentCurrencyCode();
        $rate = $this->getRate($baseCurrencyCode);

        $this->addColumn('orders_avg_amount', array(
            'header'    => $this->__('Average Order Amount'),
            'width'     => '200px',
            'align'     => 'right',
            'sortable'  => false,
            'type'      => 'currency',
            'currency_code'  => $baseCurrencyCode,
            'index'     => 'orders_avg_amount',
            'total'     => 'orders_sum_amount/orders_count',
            'renderer'  => 'Mage_Adminhtml_Block_Report_Grid_Column_Renderer_Currency',
            'rate'      => $rate,
        ));

        $this->addColumn('orders_sum_amount', array(
            'header'    => $this->__('Total Order Amount'),
            'width'     => '200px',
            'align'     => 'right',
            'sortable'  => false,
            'type'      => 'currency',
            'currency_code'  => $baseCurrencyCode,
            'index'     => 'orders_sum_amount',
            'total'     => 'sum',
            'renderer'  => 'Mage_Adminhtml_Block_Report_Grid_Column_Renderer_Currency',
            'rate'      => $rate,
        ));

        $this->addExportType('*/*/exportTotalsCsv', Mage::helper('Mage_Reports_Helper_Data')->__('CSV'));
        $this->addExportType('*/*/exportTotalsExcel', Mage::helper('Mage_Reports_Helper_Data')->__('Excel XML'));

        return parent::_prepareColumns();
    }

}
