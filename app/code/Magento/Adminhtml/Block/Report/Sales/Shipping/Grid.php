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
 * Adminhtml shipping report grid block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Report_Sales_Shipping_Grid extends Magento_Adminhtml_Block_Report_Grid_Abstract
{
    protected $_columnGroupBy = 'period';

    protected function _construct()
    {
        parent::_construct();
        $this->setCountTotals(true);
        $this->setCountSubTotals(true);
    }

    public function getResourceCollectionName()
    {
        return ($this->getFilterData()->getData('report_type') == 'created_at_shipment')
            ? 'Magento_Sales_Model_Resource_Report_Shipping_Collection_Shipment'
            : 'Magento_Sales_Model_Resource_Report_Shipping_Collection_Order';
    }

    protected function _prepareColumns()
    {
        $this->addColumn('period', array(
            'header'            => Mage::helper('Magento_Sales_Helper_Data')->__('Interval'),
            'index'             => 'period',
            'sortable'          => false,
            'period_type'       => $this->getPeriodType(),
            'renderer'          => 'Magento_Adminhtml_Block_Report_Sales_Grid_Column_Renderer_Date',
            'totals_label'      => Mage::helper('Magento_Sales_Helper_Data')->__('Total'),
            'subtotals_label'   => Mage::helper('Magento_Sales_Helper_Data')->__('Subtotal'),
            'html_decorators'   => array('nobr'),
            'header_css_class'  => 'col-period',
            'column_css_class'  => 'col-period'
        ));

        $this->addColumn('shipping_description', array(
            'header'    => Mage::helper('Magento_Sales_Helper_Data')->__('Carrier/Method'),
            'index'     => 'shipping_description',
            'sortable'  => false,
            'header_css_class'  => 'col-method',
            'column_css_class'  => 'col-method'
        ));

        $this->addColumn('orders_count', array(
            'header'    => Mage::helper('Magento_Sales_Helper_Data')->__('Orders'),
            'index'     => 'orders_count',
            'total'     => 'sum',
            'type'      => 'number',
            'sortable'  => false,
            'header_css_class'  => 'col-qty',
            'column_css_class'  => 'col-qty'
        ));

        if ($this->getFilterData()->getStoreIds()) {
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        }

        $currencyCode = $this->getCurrentCurrencyCode();
        $rate = $this->getRate($currencyCode);

        $this->addColumn('total_shipping', array(
            'header'        => Mage::helper('Magento_Sales_Helper_Data')->__('Total Sales Shipping'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_shipping',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $rate,
            'header_css_class'  => 'col-total-sales-shipping',
            'column_css_class'  => 'col-total-sales-shipping'
        ));

        $this->addColumn('total_shipping_actual', array(
            'header'        => Mage::helper('Magento_Sales_Helper_Data')->__('Total Shipping'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_shipping_actual',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $rate,
            'header_css_class'  => 'col-total-shipping',
            'column_css_class'  => 'col-total-shipping'
        ));

        $this->addExportType('*/*/exportShippingCsv', Mage::helper('Magento_Adminhtml_Helper_Data')->__('CSV'));
        $this->addExportType('*/*/exportShippingExcel', Mage::helper('Magento_Adminhtml_Helper_Data')->__('Excel XML'));

        return parent::_prepareColumns();
    }
}



