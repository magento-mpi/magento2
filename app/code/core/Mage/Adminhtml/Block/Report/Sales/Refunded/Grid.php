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
 * Adminhtml refunded report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Sales_Refunded_Grid extends Mage_Adminhtml_Block_Report_Grid_Abstract
{
    protected $_columnGroupBy = 'period';

    protected function _construct()
    {
        parent::_construct();
        $this->setCountTotals(true);
    }

    public function getResourceCollectionName()
    {
        return ($this->getFilterData()->getData('report_type') == 'created_at_refunded')
            ? 'Mage_Sales_Model_Resource_Report_Refunded_Collection_Refunded'
            : 'Mage_Sales_Model_Resource_Report_Refunded_Collection_Order';
    }

    protected function _prepareColumns()
    {
        $this->addColumn('period', array(
            'header'        => Mage::helper('Mage_Sales_Helper_Data')->__('Period'),
            'index'         => 'period',
            'width'         => 100,
            'sortable'      => false,
            'period_type'   => $this->getPeriodType(),
            'renderer'      => 'Mage_Adminhtml_Block_Report_Sales_Grid_Column_Renderer_Date',
            'totals_label'  => Mage::helper('Mage_Sales_Helper_Data')->__('Total'),
            'html_decorators' => array('nobr'),
        ));

        $this->addColumn('orders_count', array(
            'header'    => Mage::helper('Mage_Sales_Helper_Data')->__('Number of Refunded Orders'),
            'index'     => 'orders_count',
            'type'      => 'number',
            'total'     => 'sum',
            'sortable'  => false
        ));

        if ($this->getFilterData()->getStoreIds()) {
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        }
        $currencyCode = $this->getCurrentCurrencyCode();
        $rate = $this->getRate($currencyCode);

        $this->addColumn('refunded', array(
            'header'        => Mage::helper('Mage_Sales_Helper_Data')->__('Total Refunded'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'refunded',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $rate,
        ));

        $this->addColumn('online_refunded', array(
            'header'        => Mage::helper('Mage_Sales_Helper_Data')->__('Online Refunded'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'online_refunded',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $rate,
        ));

        $this->addColumn('offline_refunded', array(
            'header'        => Mage::helper('Mage_Sales_Helper_Data')->__('Offline Refunded'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'offline_refunded',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $rate,
        ));

        $this->addExportType('*/*/exportRefundedCsv', Mage::helper('Mage_Adminhtml_Helper_Data')->__('CSV'));
        $this->addExportType('*/*/exportRefundedExcel', Mage::helper('Mage_Adminhtml_Helper_Data')->__('Excel XML'));

        return parent::_prepareColumns();
    }
}
