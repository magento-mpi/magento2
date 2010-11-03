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
 * Order entity resource model
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Report_Order extends Mage_Sales_Model_Resource_Report_Abstract
{
    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('sales/order_aggregated_created', 'id');
    }

    /**
     * Aggregate Orders data by order created at
     *
     * @param mixed $from
     * @param mixed $to
     * @return Mage_Sales_Model_Resource_Report_Order
     */
    public function aggregate($from = null, $to = null)
    {
        // convert input dates to UTC to be comparable with DATETIME fields in DB
        $from = $this->_dateToUtc($from);
        $to   = $this->_dateToUtc($to);

        $this->_checkDates($from, $to);
        $adapter = $this->_getWriteAdapter();

        $adapter->beginTransaction();

        try {
            if ($from !== null || $to !== null) {
                $subSelect = $this->_getTableDateRangeSelect(
                    $this->getTable('sales/order'),
                    'created_at', 'updated_at', $from, $to
                );
            } else {
                $subSelect = null;
            }

            $this->_clearTableByDateRange($this->getMainTable(), $from, $to, $subSelect);
            // convert dates from UTC to current admin timezone
            $periodExpr                  = $adapter->getDateAddSql('o.created_at', $this->_getStoreTimezoneUtcOffset(),
                Varien_Db_Adapter_Interface::INTERVAL_HOUR);
            
            $ifnullBaseTotalCanceled     = $adapter->getCheckSql('o.base_total_canceled IS NULL', 0,
                'o.base_total_canceled');
            $ifnullBaseTotalRefunded     = $adapter->getCheckSql('o.base_total_refunded IS NULL', 0,
                'o.base_total_refunded');
            $ifnullBaseTaxInvoiced       = $adapter->getCheckSql('o.base_tax_invoiced IS NULL', 0,
                'o.base_tax_invoiced');
            $ifnullBaseShippingInvoiced  = $adapter->getCheckSql('o.base_shipping_invoiced IS NULL', 0,
                'o.base_shipping_invoiced');
            $ifnullBaseTotalInvoicedCost = $adapter->getCheckSql('o.base_total_invoiced_cost IS NULL', 0,
                'o.base_total_invoiced_cost');

            $ifnullBaseTaxCanceled       = $adapter->getCheckSql('o.base_tax_canceled IS NULL', 0,
                'o.base_tax_canceled');
            $ifnullBaseTaxRefunded       = $adapter->getCheckSql('o.base_tax_refunded IS NULL', 0,
                'o.base_tax_refunded');
            $ifnullBaseShippingCanceled  = $adapter->getCheckSql('o.base_shipping_canceled IS NULL', 0,
                'o.base_shipping_canceled');
            $ifnullBaseShippingRefunded  = $adapter->getCheckSql('o.base_shipping_refunded IS NULL', 0,
                'o.base_shipping_refunded');
            $ifnullBaseDiscountCanceled  = $adapter->getCheckSql('o.base_discount_canceled IS NULL', 0,
                'o.base_discount_canceled');
            $ifnullBaseDiscountRefunded  = $adapter->getCheckSql('o.base_discount_refunded IS NULL', 0,
                'o.base_discount_refunded');

            $columns = array(
                // convert dates from UTC to current admin timezone
                'period'                         => $periodExpr,
                'store_id'                       => 'o.store_id',
                'order_status'                   => 'o.status',
                'orders_count'                   => 'COUNT(o.entity_id)',
                'total_qty_ordered'              => 'SUM(oi.total_qty_ordered)',
                'total_qty_invoiced'             => 'SUM(oi.total_qty_invoiced)',
                'total_income_amount'            => "SUM((o.base_grand_total - {$ifnullBaseTotalCanceled})"
                    . ' * o.base_to_global_rate)',
                'total_revenue_amount'           => "SUM((o.base_total_paid - {$ifnullBaseTotalRefunded})"
                    . ' * o.base_to_global_rate)',
                'total_profit_amount'            => "SUM((o.base_total_paid - {$ifnullBaseTotalRefunded}"
                    . " - {$ifnullBaseTaxInvoiced} - {$ifnullBaseShippingInvoiced} - {$ifnullBaseTotalInvoicedCost})"
                    . ' * o.base_to_global_rate)',
                'total_invoiced_amount'          => 'SUM(o.base_total_invoiced * o.base_to_global_rate)',
                'total_canceled_amount'          => 'SUM(o.base_total_canceled * o.base_to_global_rate)',
                'total_paid_amount'              => 'SUM(o.base_total_paid * o.base_to_global_rate)',
                'total_refunded_amount'          => 'SUM(o.base_total_refunded * o.base_to_global_rate)',
                'total_tax_amount'               => "SUM((o.base_tax_amount - {$ifnullBaseTaxCanceled})"
                    . ' * o.base_to_global_rate)',
                'total_tax_amount_actual'        => "SUM((o.base_tax_invoiced - {$ifnullBaseTaxRefunded})"
                    . ' * o.base_to_global_rate)',
                'total_shipping_amount'          => "SUM((o.base_shipping_amount - {$ifnullBaseShippingCanceled})"
                    . ' * o.base_to_global_rate)',
                'total_shipping_amount_actual'   => "SUM((o.base_shipping_invoiced - {$ifnullBaseShippingRefunded})"
                    . ' * o.base_to_global_rate)',
                'total_discount_amount'          => "SUM((ABS(o.base_discount_amount) - {$ifnullBaseDiscountCanceled})"
                    . ' * o.base_to_global_rate)',
                'total_discount_amount_actual'   => "SUM((o.base_discount_invoiced - {$ifnullBaseDiscountRefunded})"
                    . ' * o.base_to_global_rate)',
            );

            $select          = $adapter->select();
            $selectOrderItem = $adapter->select();

            $qtyCanceledExpr = $adapter->getCheckSql('qty_canceled IS NULL', 0, 'qty_canceled');
            $cols            = array(
                'order_id'           => 'order_id',
                'total_qty_ordered'  => "SUM(qty_ordered - {$qtyCanceledExpr})",
                'total_qty_invoiced' => 'SUM(qty_invoiced)',
            );
            $selectOrderItem->from($this->getTable('sales/order_item'), $cols)
                ->group('order_id');

            $select->from(array('o' => $this->getTable('sales/order')), $columns)
                ->join(array('oi' => $selectOrderItem), 'oi.order_id = o.entity_id', array())
                ->where('o.state NOT IN (?)', array(
                    Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                    Mage_Sales_Model_Order::STATE_NEW,
                    Mage_Sales_Model_Order::STATE_CANCELED,
                ));

            if ($subSelect !== null) {
                $select->where($this->_makeConditionFromDateRangeSelect($subSelect, 'o.created_at'));
            }

            $select->group(array(
                $periodExpr,
                'o.store_id',
                'o.status',
            ));

            // setup all columns to select SUM() except period, store_id and order_status
            foreach ($columns as $k => $v) {
                $columns[$k] = 'SUM(' . $k . ')';
            }
            $columns['period']         = 'period';
            $columns['store_id']       = new Zend_Db_Expr(Mage_Core_Model_App::ADMIN_STORE_ID);
            $columns['order_status']   = 'order_status';

            $select->reset();
            $select->from($this->getMainTable(), $columns)
                ->where('store_id <> 0');

            if ($subSelect !== null) {
                $select->where($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
            }

            $select->group(array(
                'period',
                'order_status'
            ));
            $adapter->query($select->insertFromSelect($this->getMainTable(), array_keys($columns)));
            $this->_setFlagData(Mage_Reports_Model_Flag::REPORT_ORDER_FLAG_CODE);
            $adapter->commit();
        } catch (Exception $e) {
            $adapter->rollBack();
            throw $e;
        }
        return $this;
    }
}
