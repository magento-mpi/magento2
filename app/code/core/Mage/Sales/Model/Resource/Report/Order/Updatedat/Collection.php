<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Report order updated_at collection
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Report_Order_Updatedat_Collection
    extends Mage_Sales_Model_Resource_Report_Collection_Abstract
{
    /**
     * Period format
     *
     * @var string
     */
    protected $_periodFormat;

    /**
     * Is inited
     *
     * @var bool
     */
    protected $_inited             = false;

    /**
     * Selected columns
     *
     * @var array
     */
    protected $_selectedColumns    = array();

    /**
     * Initialize custom resource model
     *
     */
    public function __construct()
    {
        parent::_construct();
        $this->setModel('adminhtml/report_item');
        $this->_resource = Mage::getResourceModel('sales/report')->init('sales/order', 'entity_id');
        $this->setConnection($this->getResource()->getReadConnection());
    }

    /**
     * Apply stores filter
     *
     * @return Mage_Sales_Model_Resource_Report_Order_Updatedat_Collection
     */
    protected function _applyStoresFilter()
    {
        $nullCheck = false;
        $storeIds = $this->_storesIds;

        if (!is_array($storeIds)) {
            $storeIds = array($storeIds);
        }

        $storeIds = array_unique($storeIds);

        if ($index = array_search(null, $storeIds)) {
            unset($storeIds[$index]);
            $nullCheck = true;
        }

        if ($nullCheck) {
            $this->getSelect()->where('store_id IN(?) OR store_id IS NULL', $storeIds);
        } elseif ($storeIds[0] != '') {
            $this->getSelect()->where('store_id IN(?)', $storeIds);
        }

        return $this;
    }

    /**
     * Apply order status filter
     *
     * @return Mage_Sales_Model_Resource_Report_Order_Updatedat_Collection
     */
    protected function _applyOrderStatusFilter()
    {
        if (is_null($this->_orderStatus)) {
            return $this;
        }
        $orderStatus = $this->_orderStatus;
        if (!is_array($orderStatus)) {
            $orderStatus = array($orderStatus);
        }
        $this->getSelect()->where('status IN(?)', $orderStatus);
        return $this;
    }

    /**
     * Retrieve array of columns to select
     *
     * @return array
     */
    protected function _getSelectedColumns()
    {
        $adapter                 = $this->getConnection();
        $ifnullBaseTotalCanceled = $adapter->getCheckSql('e.base_total_canceled IS NULL', 0, 'e.base_total_canceled');
        $totalIncomeAmount       = "SUM((e.base_grand_total - {$ifnullBaseTotalCanceled}) * e.base_to_global_rate)";

        $ifnullBaseTotalRefunded = $adapter->getCheckSql('e.base_total_refunded IS NULL', 0, 'e.base_total_refunded');
        $totalRevenueAmount      = "SUM((e.base_total_paid - {$ifnullBaseTotalRefunded}) * e.base_to_global_rate)";

        $ifnullTotalRefunded     = $adapter->getCheckSql('e.base_total_refunded IS NULL', 0, 'e.base_total_refunded');
        $ifnullBaseTaxInvoiced   = $adapter->getCheckSql('e.base_tax_invoiced IS NULL', 0, 'e.base_tax_invoiced');
        $ifnullBaseShippingInvoiced = $adapter->getCheckSql('e.base_shipping_invoiced IS NULL', 0,
            'e.base_shipping_invoiced');
        $ifnullBaseTotalInvoicedCost = $adapter
            ->getCheckSql('e.base_total_invoiced_cost IS NULL', 0, 'e.base_total_invoiced_cost');
        $totalProfitAmountSum    = new Zend_Db_Expr(
            "SUM((e.base_total_paid - "
            . $ifnullTotalRefunded . " - " 
            . $ifnullBaseTaxInvoiced . " - " 
            . $ifnullBaseShippingInvoiced ." - " 
            . $ifnullBaseTotalInvoicedCost
            . ") * e.base_to_global_rate)"
        );

        $ifnullBaseTaxCanceled   = $adapter->getCheckSql('e.base_tax_canceled IS NULL', 0, 'e.base_tax_canceled');
        $sumTotalTaxAmount       = new Zend_Db_Expr(
            "SUM((e.base_tax_amount - {$ifnullBaseTaxCanceled}) * e.base_to_global_rate)"
        );

        $ifnullBaseTaxRefunded   = $adapter->getCheckSql('e.base_tax_refunded IS NULL', 0, 'e.base_tax_refunded');
        $sumTotalTaxAmountActual = new Zend_Db_Expr(
            "SUM((e.base_tax_invoiced - {$ifnullBaseTaxRefunded}) * e.base_to_global_rate)"
        );

        $ifnullBaseShippingCanceled = $adapter->getCheckSql('e.base_shipping_canceled IS NULL', 0,
            'e.base_shipping_canceled');
        $sumTotalShippingAmount = new Zend_Db_Expr(
            "SUM((e.base_shipping_amount - {$ifnullBaseShippingCanceled}) * e.base_to_global_rate)"
        );

        $ifnullBaseShippingRefunded = $adapter->getCheckSql('e.base_shipping_refunded IS NULL', 0,
            'e.base_shipping_refunded');
        $sumTotalShippingAmountActual = new Zend_Db_Expr(
            "SUM((e.base_shipping_invoiced - {$ifnullBaseShippingRefunded}) * e.base_to_global_rate)"
        );

        $ifnullBaseDiscountCanceled = $adapter->getCheckSql('e.base_discount_canceled IS NULL', 0,
            'e.base_discount_canceled');
        $sumTotalDiscountAmount = new Zend_Db_Expr(
            "SUM((ABS(e.base_discount_amount) - {$ifnullBaseDiscountCanceled}) * e.base_to_global_rate)"
        );

        $ifnullBaseDiscountRefunded = $adapter->getCheckSql('e.base_discount_refunded IS NULL', 0,
            'e.base_discount_refunded');
        $sumTotalDiscountAmountActual = new Zend_Db_Expr(
            "SUM((e.base_discount_invoiced - {$ifnullBaseDiscountRefunded}) * e.base_to_global_rate)"
        );

        $this->_selectedColumns    = array(
            'orders_count'                   => 'COUNT(e.entity_id)',
            'total_qty_ordered'              => $adapter->getCheckSql('SUM(oi.total_qty_ordered) IS NULL', 0,
                'SUM(oi.total_qty_ordered)'),
            'total_qty_invoiced'             => $adapter->getCheckSql('SUM(oi.total_qty_invoiced) IS NULL', 0,
                'SUM(oi.total_qty_invoiced)'),
            'total_income_amount'            => $adapter->getCheckSql($totalIncomeAmount . ' IS NULL', 0,
                $totalIncomeAmount),
            'total_revenue_amount'           => $adapter->getCheckSql($totalRevenueAmount . ' IS NULL', 0,
                $totalRevenueAmount),
            'total_profit_amount'            => $adapter->getCheckSql($totalProfitAmountSum . ' IS NULL', 0,
                $totalProfitAmountSum),
            'total_invoiced_amount'          => $adapter->getCheckSql(
                'SUM(e.base_total_invoiced * e.base_to_global_rate) IS NULL', 0,
                'SUM(e.base_total_invoiced * e.base_to_global_rate)'),
            'total_canceled_amount'          => $adapter->getCheckSql(
                'SUM(e.base_total_canceled * e.base_to_global_rate) IS NULL', 0,
                'SUM(e.base_total_canceled * e.base_to_global_rate)'),
            'total_paid_amount'              => $adapter->getCheckSql(
                'SUM(e.base_total_paid * e.base_to_global_rate) IS NULL', 0,
                'SUM(e.base_total_paid * e.base_to_global_rate)'),
            'total_refunded_amount'          => $adapter->getCheckSql(
                'SUM(e.base_total_refunded * e.base_to_global_rate) IS NULL', 0,
                'SUM(e.base_total_refunded * e.base_to_global_rate)'),
            'total_tax_amount'               => $adapter->getCheckSql($sumTotalTaxAmount . ' IS NULL', 0,
                $sumTotalTaxAmount),
            'total_tax_amount_actual'        => $adapter->getCheckSql($sumTotalTaxAmountActual . ' IS NULL', 0,
                $sumTotalTaxAmountActual),
            'total_shipping_amount'          => $adapter->getCheckSql($sumTotalShippingAmount . ' IS NULL', 0,
                $sumTotalShippingAmount),
            'total_shipping_amount_actual'   => $adapter->getCheckSql($sumTotalShippingAmountActual . ' IS NULL', 0,
                $sumTotalShippingAmountActual),
            'total_discount_amount'          => $adapter->getCheckSql($sumTotalDiscountAmount . ' IS NULL', 0,
                $sumTotalDiscountAmount),
            'total_discount_amount_actual'   => $adapter->getCheckSql($sumTotalDiscountAmountActual . ' IS NULL', 0,
                $sumTotalDiscountAmountActual),
        );
        
        if (!$this->isTotals()) {
            if ('month' == $this->_period) {
                $this->_periodFormat = $adapter->getDateFormatSql('e.updated_at', '%Y-%m');
            } elseif ('year' == $this->_period) {
                $this->_periodFormat = $adapter->getDateExtractSql('e.updated_at',
                    Varien_Db_Adapter_Interface::INTERVAL_YEAR);
            } else {
                $this->_periodFormat = $adapter->getDatePartSql('e.updated_at');
            }
            $this->_selectedColumns['period'] = $this->_periodFormat;
        }
        return $this->_selectedColumns;
    }

    /**
     * Add selected data
     *
     * @return Mage_Sales_Model_Resource_Report_Order_Updatedat_Collection
     */
    protected function _initSelect()
    {
        if ($this->_inited) {
            return $this;
        }

        $columns           = $this->_getSelectedColumns();
        $adapter           = $this->getConnection();
        $mainTable         = $this->getResource()->getMainTable();
        $ifnullQtyCanceled = $adapter->getCheckSql('qty_canceled IS NULL', 0, 'qty_canceled');
        $selectOrderItem   = $adapter->select()
            ->from($this->getTable('sales/order_item'), array(
                'order_id'           => 'order_id',
                'total_qty_ordered'  => "SUM(qty_ordered - {$ifnullQtyCanceled})",
                'total_qty_invoiced' => 'SUM(qty_invoiced)',
            ))
            ->group('order_id');

        $select = $this->getSelect()
            ->from(array('e' => $mainTable), $columns)
            ->join(array('oi' => $selectOrderItem), 'oi.order_id = e.entity_id', array())
            ->where('e.state NOT IN (?)', array(
                    Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                    Mage_Sales_Model_Order::STATE_NEW,
                    Mage_Sales_Model_Order::STATE_CANCELED,
                ));

        $this->_applyStoresFilter();
        $this->_applyOrderStatusFilter();

        $datePattern = $adapter->getDatePartSql('?');
        $dateUpdatedAt = $adapter->getDatePartSql('e.updated_at');
        if ($this->_to !== null) {
            $select->where("{$dateUpdatedAt} <= {$datePattern}", $this->_to);
        }

        if ($this->_from !== null) {
            $select->where("{$dateUpdatedAt} >= {$datePattern}", $this->_from);
        }

        if (!$this->isTotals()) {
            $select->group($this->_periodFormat);
        }

        $this->_inited = true;
        return $this;
    }

    /**
     * Load
     *
     * @param boolean $printQuery
     * @param boolean $logQuery
     * @return Mage_Sales_Model_Resource_Report_Order_Updatedat_Collection
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }
        $this->_initSelect();
        $this->setApplyFilters(false);
        return parent::load($printQuery, $logQuery);
    }
}
