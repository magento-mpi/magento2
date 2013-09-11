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
 * Adminhtml invoiced report grid block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Report\Sales\Invoiced;

class Grid extends \Magento\Adminhtml\Block\Report\Grid\AbstractGrid
{
    protected $_columnGroupBy = 'period';

    protected function _construct()
    {
        parent::_construct();
        $this->setCountTotals(true);
    }

    public function getResourceCollectionName()
    {
        return ($this->getFilterData()->getData('report_type') == 'created_at_invoice')
            ? '\Magento\Sales\Model\Resource\Report\Invoiced\Collection\Invoiced'
            : '\Magento\Sales\Model\Resource\Report\Invoiced\Collection\Order';
    }

    protected function _prepareColumns()
    {
        $this->addColumn('period', array(
            'header'        => __('Interval'),
            'index'         => 'period',
            'sortable'      => false,
            'period_type'   => $this->getPeriodType(),
            'renderer'      => '\Magento\Adminhtml\Block\Report\Sales\Grid\Column\Renderer\Date',
            'totals_label'  => __('Total'),
            'html_decorators' => array('nobr'),
            'header_css_class'  => 'col-period',
            'column_css_class'  => 'col-period'
        ));

        $this->addColumn('orders_count', array(
            'header'    => __('Orders'),
            'index'     => 'orders_count',
            'type'      => 'number',
            'total'     => 'sum',
            'sortable'  => false,
            'header_css_class'  => 'col-qty',
            'column_css_class'  => 'col-qty'
        ));

        $this->addColumn('orders_invoiced', array(
            'header'    => __('Invoiced Orders'),
            'index'     => 'orders_invoiced',
            'type'      => 'number',
            'total'     => 'sum',
            'sortable'  => false,
            'header_css_class'  => 'col-invoiced',
            'column_css_class'  => 'col-invoiced'
        ));

        if ($this->getFilterData()->getStoreIds()) {
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        }
        $currencyCode = $this->getCurrentCurrencyCode();
        $rate = $this->getRate($currencyCode);

        $this->addColumn('invoiced', array(
            'header'        => __('Total Invoiced'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'invoiced',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $rate,
            'header_css_class'  => 'col-total-invoiced',
            'column_css_class'  => 'col-total-invoiced'
        ));

        $this->addColumn('invoiced_captured', array(
            'header'        => __('Paid Invoices'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'invoiced_captured',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $rate,
            'header_css_class'  => 'col-total-invoiced-paid',
            'column_css_class'  => 'col-total-invoiced-paid'
        ));

        $this->addColumn('invoiced_not_captured', array(
            'header'        => __('Unpaid Invoices'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'invoiced_not_captured',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $rate,
            'header_css_class'  => 'col-total-invoiced-not-paid',
            'column_css_class'  => 'col-total-invoiced-not-paid'
        ));

        $this->addExportType('*/*/exportInvoicedCsv', __('CSV'));
        $this->addExportType('*/*/exportInvoicedExcel', __('Excel XML'));

        return parent::_prepareColumns();
    }
}
