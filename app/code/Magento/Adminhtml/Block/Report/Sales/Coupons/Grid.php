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
 * Adminhtml coupons report grid block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Report_Sales_Coupons_Grid extends Magento_Adminhtml_Block_Report_Grid_Abstract
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
        if (($this->getFilterData()->getData('report_type') == 'updated_at_order')) {
            return 'Magento_SalesRule_Model_Resource_Report_Updatedat_Collection';
        } else {
            return 'Magento_SalesRule_Model_Resource_Report_Collection';
        }
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

        $this->addColumn('coupon_code', array(
            'header'    => __('Coupon Code'),
            'sortable'  => false,
            'index'     => 'coupon_code',
            'header_css_class'  => 'col-code',
            'column_css_class'  => 'col-code'
        ));

        $this->addColumn('rule_name', array(
            'header'    => __('Price Rule'),
            'sortable'  => false,
            'index'     => 'rule_name',
            'header_css_class'  => 'col-rule',
            'column_css_class'  => 'col-rule'
        ));

        $this->addColumn('coupon_uses', array(
            'header'    => __('Uses'),
            'sortable'  => false,
            'index'     => 'coupon_uses',
            'total'     => 'sum',
            'type'      => 'number',
            'header_css_class'  => 'col-users',
            'column_css_class'  => 'col-users'
        ));

        if ($this->getFilterData()->getStoreIds()) {
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        }
        $currencyCode = $this->getCurrentCurrencyCode();
        $rate = $this->getRate($currencyCode);

        $this->addColumn('subtotal_amount', array(
            'header'        => __('Sales Subtotal'),
            'sortable'      => false,
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'         => 'sum',
            'index'         => 'subtotal_amount',
            'rate'          => $rate,
            'header_css_class'  => 'col-sales',
            'column_css_class'  => 'col-sales'
        ));

        $this->addColumn('discount_amount', array(
            'header'        => __('Sales Discount'),
            'sortable'      => false,
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'         => 'sum',
            'index'         => 'discount_amount',
            'rate'          => $rate,
            'header_css_class'  => 'col-sales-discount',
            'column_css_class'  => 'col-sales-discount'
        ));

        $this->addColumn('total_amount', array(
            'header'        => __('Sales Total'),
            'sortable'      => false,
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'         => 'sum',
            'index'         => 'total_amount',
            'rate'          => $rate,
            'header_css_class'  => 'col-total-amount',
            'column_css_class'  => 'col-total-amount'
        ));

        $this->addColumn('subtotal_amount_actual', array(
            'header'        => __('Subtotal'),
            'sortable'      => false,
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'         => 'sum',
            'index'         => 'subtotal_amount_actual',
            'rate'          => $rate,
            'header_css_class'  => 'col-subtotal',
            'column_css_class'  => 'col-subtotal'
        ));

        $this->addColumn('discount_amount_actual', array(
            'header'        => __('Discount'),
            'sortable'      => false,
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'         => 'sum',
            'index'         => 'discount_amount_actual',
            'rate'          => $rate,
            'header_css_class'  => 'col-discount',
            'column_css_class'  => 'col-discount'
        ));

        $this->addColumn('total_amount_actual', array(
            'header'        => __('Total'),
            'sortable'      => false,
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'         => 'sum',
            'index'         => 'total_amount_actual',
            'rate'          => $rate,
            'header_css_class'  => 'col-total',
            'column_css_class'  => 'col-total'
        ));

        $this->addExportType('*/*/exportCouponsCsv', __('CSV'));
        $this->addExportType('*/*/exportCouponsExcel', __('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * Add price rule filter
     *
     * @param Magento_Reports_Model_Resource_Report_Collection_Abstract $collection
     * @param \Magento\Object $filterData
     * @return Magento_Adminhtml_Block_Report_Grid_Abstract
     */
    protected function _addCustomFilter($collection, $filterData)
    {
        if ($filterData->getPriceRuleType()) {
            $rulesList = $filterData->getData('rules_list');
            if (isset($rulesList[0])) {
                $rulesIds = explode(',', $rulesList[0]);
                $collection->addRuleFilter($rulesIds);
            }
        }

        return parent::_addCustomFilter($filterData, $collection);
    }
}
