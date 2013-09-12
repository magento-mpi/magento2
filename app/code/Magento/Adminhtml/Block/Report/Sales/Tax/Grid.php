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
 * Adminhtml tax report grid block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Report_Sales_Tax_Grid extends Magento_Adminhtml_Block_Report_Grid_Abstract
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
        return ($this->getFilterData()->getData('report_type') == 'updated_at_order')
            ? 'Magento_Tax_Model_Resource_Report_Updatedat_Collection'
            : 'Magento_Tax_Model_Resource_Report_Collection';
    }

    protected function _prepareColumns()
    {
        $this->addColumn('period', array(
            'header'            => __('Interval'),
            'index'             => 'period',
            'sortable'          => false,
            'period_type'       => $this->getPeriodType(),
            'renderer'          => 'Magento_Adminhtml_Block_Report_Sales_Grid_Column_Renderer_Date',
            'totals_label'      => __('Total'),
            'subtotals_label'   => __('Subtotal'),
            'html_decorators' => array('nobr'),
            'header_css_class'  => 'col-period',
            'column_css_class'  => 'col-period'
        ));

        $this->addColumn('code', array(
            'header'    => __('Tax'),
            'index'     => 'code',
            'type'      => 'string',
            'sortable'  => false,
            'header_css_class'  => 'col-tax-name',
            'column_css_class'  => 'col-tax-name'
        ));

        $this->addColumn('percent', array(
            'header'    => __('Rate'),
            'index'     => 'percent',
            'type'      => 'number',
            'sortable'  => false,
            'header_css_class'  => 'col-rate',
            'column_css_class'  => 'col-rate'
        ));

        $this->addColumn('orders_count', array(
            'header'    => __('Orders'),
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

        $this->addColumn('tax_base_amount_sum', array(
            'header'        => __('Tax Amount'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'tax_base_amount_sum',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $this->getRate($currencyCode),
            'header_css_class'  => 'col-tax-amount',
            'column_css_class'  => 'col-tax-amount'
        ));

        $this->addExportType('*/*/exportTaxCsv', __('CSV'));
        $this->addExportType('*/*/exportTaxExcel', __('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * Preparing collection
     * Filter canceled statuses for orders in taxes
     *
     *@return Magento_Adminhtml_Block_Report_Sales_Tax_Grid
     */
    protected function _prepareCollection()
    {
        $filterData = $this->getFilterData();
        if(!$filterData->hasData('order_statuses')) {
            $orderConfig = Mage::getModel('Magento_Sales_Model_Order_Config');
            $statusValues = array();
            $canceledStatuses = $orderConfig->getStateStatuses(Magento_Sales_Model_Order::STATE_CANCELED);
            foreach ($orderConfig->getStatuses() as $code => $label) {
                if (!isset($canceledStatuses[$code])) {
                    $statusValues[] = $code;
                }
            }
            $filterData->setOrderStatuses($statusValues);
        }
        return parent::_prepareCollection();
    }
}
