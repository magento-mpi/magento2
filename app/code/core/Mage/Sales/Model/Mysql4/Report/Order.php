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
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order entity resource model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Sales_Model_Mysql4_Report_Order extends Mage_Sales_Model_Mysql4_Report_Abstract
{
    protected function _construct()
    {
        $this->_init('sales/order_aggregated_created', 'id');
    }

    /**
     * Aggregate Orders data by order created at
     *
     * @param mixed $from
     * @param mixed $to
     * @return Mage_Sales_Model_Mysql4_Order
     */
    public function aggregate($from = null, $to = null)
    {
        // convert input dates to UTC to be comparable with DATETIME fields in DB
        $from = $this->_dateToUtc($from);
        $to = $this->_dateToUtc($to);

        $this->_checkDates($from, $to);
        $this->_getWriteAdapter()->beginTransaction();

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

            $columns = array(
                // convert dates from UTC to current admin timezone
                'period'                         => 'DATE(CONVERT_TZ(source_table.created_at, "+00:00", "' . $this->_getStoreTimezoneUtcOffset() . '"))',
                'store_id'                       => 'source_table.store_id',
                'order_status'                   => 'source_table.status',
                'orders_count'                   => 'COUNT(source_table.entity_id)',
                'total_qty_ordered'              => 'SUM(source_table.total_qty_ordered)',
                'base_profit_amount'             => 'SUM(IFNULL(source_table.base_subtotal_invoiced, 0) * source_table.base_to_global_rate) + SUM(IFNULL(source_table.base_discount_refunded, 0) * source_table.base_to_global_rate) - SUM(IFNULL(source_table.base_subtotal_refunded, 0) * source_table.base_to_global_rate) - SUM(IFNULL(source_table.base_discount_invoiced, 0) * source_table.base_to_global_rate) - SUM(IFNULL(source_table.base_total_invoiced_cost, 0) * source_table.base_to_global_rate)',
                'base_subtotal_amount'           => 'SUM(source_table.base_subtotal * source_table.base_to_global_rate)',
                'base_subtotal_invoiced_amount'  => 'SUM(IFNULL(source_table.base_subtotal_invoiced, 0) * source_table.base_to_global_rate)',
                'base_subtotal_canceled_amount'  => 'SUM(IFNULL(source_table.base_subtotal_canceled, 0) * source_table.base_to_global_rate)',
                'base_subtotal_refunded_amount'  => 'SUM(IFNULL(source_table.base_subtotal_refunded, 0) * source_table.base_to_global_rate)',
                'base_tax_amount'                => 'SUM(source_table.base_tax_amount * source_table.base_to_global_rate)',
                'base_tax_invoiced_amount'       => 'SUM(IFNULL(source_table.base_tax_invoiced, 0) * source_table.base_to_global_rate)',
                'base_tax_canceled_amount'       => 'SUM(IFNULL(source_table.base_tax_canceled, 0) * source_table.base_to_global_rate)',
                'base_tax_refunded_amount'       => 'SUM(IFNULL(source_table.base_tax_refunded, 0) * source_table.base_to_global_rate)',
                'base_shipping_amount'           => 'SUM(source_table.base_shipping_amount * source_table.base_to_global_rate)',
                'base_shipping_invoiced_amount'  => 'SUM(IFNULL(source_table.base_shipping_invoiced, 0) * source_table.base_to_global_rate)',
                'base_shipping_canceled_amount'  => 'SUM(IFNULL(source_table.base_shipping_canceled, 0) * source_table.base_to_global_rate)',
                'base_shipping_refunded_amount'  => 'SUM(IFNULL(source_table.base_shipping_refunded, 0) * source_table.base_to_global_rate)',
                'base_shipping_tax_amount'       => 'SUM(IFNULL(source_table.base_shipping_tax_amount, 0) * source_table.base_to_global_rate)',
                'base_shipping_tax_refunded_amount' => 'SUM(IFNULL(source_table.base_shipping_tax_refunded, 0) * source_table.base_to_global_rate)',
                'base_shipping_discount_amount'  => 'SUM(IFNULL(source_table.base_shipping_discount_amount, 0) * source_table.base_to_global_rate)',
                'base_discount_amount'           => 'SUM(source_table.base_discount_amount * source_table.base_to_global_rate)',
                'base_discount_invoiced_amount'  => 'SUM(IFNULL(source_table.base_discount_invoiced, 0) * source_table.base_to_global_rate)',
                'base_discount_canceled_amount'  => 'SUM(IFNULL(source_table.base_discount_canceled, 0) * source_table.base_to_global_rate)',
                'base_discount_refunded_amount'  => 'SUM(IFNULL(source_table.base_discount_refunded, 0) * source_table.base_to_global_rate)',
                'base_grand_total_amount'        => 'SUM(source_table.base_grand_total * source_table.base_to_global_rate)',
                'base_invoiced_amount'           => 'SUM(source_table.base_total_paid * source_table.base_to_global_rate)',
                'base_refunded_amount'           => 'SUM(source_table.base_total_refunded * source_table.base_to_global_rate)',
                'base_canceled_amount'           => 'SUM(IFNULL(source_table.subtotal_canceled, 0) * source_table.base_to_global_rate)'
            );

            $select = $this->_getWriteAdapter()->select();

            $select->from(array('source_table' => $this->getTable('sales/order')), $columns)
                ->where('source_table.state NOT IN (?)', array(
                    Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                    Mage_Sales_Model_Order::STATE_NEW
                ));

            if ($subSelect !== null) {
                $select->where($this->_makeConditionFromDateRangeSelect($subSelect, 'source_table.created_at'));
            }

            $select->group(new Zend_Db_Expr('1,2,3'));

            $this->_getWriteAdapter()->query($select->insertFromSelect($this->getMainTable(), array_keys($columns)));

            $columns = array(
                'period'                         => 'period',
                'store_id'                       => new Zend_Db_Expr('0'),
                'order_status'                   => 'order_status',
                'orders_count'                   => 'SUM(orders_count)',
                'total_qty_ordered'              => 'SUM(total_qty_ordered)',
                'base_profit_amount'             => 'SUM(base_profit_amount)',
                'base_subtotal_amount'           => 'SUM(base_subtotal_amount)',
                'base_subtotal_invoiced_amount'  => 'SUM(base_subtotal_invoiced_amount)',
                'base_subtotal_canceled_amount'  => 'SUM(base_subtotal_canceled_amount)',
                'base_subtotal_refunded_amount'  => 'SUM(base_subtotal_refunded_amount)',
                'base_tax_amount'                => 'SUM(base_tax_amount)',
                'base_tax_invoiced_amount'       => 'SUM(base_tax_invoiced_amount)',
                'base_tax_canceled_amount'       => 'SUM(base_tax_canceled_amount)',
                'base_tax_refunded_amount'       => 'SUM(base_tax_refunded_amount)',
                'base_shipping_amount'           => 'SUM(base_shipping_amount)',
                'base_shipping_invoiced_amount'  => 'SUM(base_shipping_invoiced_amount)',
                'base_shipping_canceled_amount'  => 'SUM(base_shipping_canceled_amount)',
                'base_shipping_refunded_amount'  => 'SUM(base_shipping_refunded_amount)',
                'base_shipping_tax_amount'       => 'SUM(base_shipping_tax_amount)',
                'base_shipping_tax_refunded_amount' => 'SUM(base_shipping_tax_refunded_amount)',
                'base_shipping_discount_amount'  => 'SUM(base_shipping_discount_amount)',
                'base_discount_amount'           => 'SUM(base_discount_amount)',
                'base_discount_invoiced_amount'  => 'SUM(base_discount_invoiced_amount)',
                'base_discount_canceled_amount'  => 'SUM(base_discount_canceled_amount)',
                'base_discount_refunded_amount'  => 'SUM(base_discount_refunded_amount)',
                'base_grand_total_amount'        => 'SUM(base_grand_total_amount)',
                'base_invoiced_amount'           => 'SUM(base_invoiced_amount)',
                'base_refunded_amount'           => 'SUM(base_refunded_amount)',
                'base_canceled_amount'           => 'SUM(base_canceled_amount)'
            );

            $select->reset();
            $select->from($this->getMainTable(), $columns)
                ->where("store_id <> 0");

            if ($subSelect !== null) {
                $select->where($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
            }

            $select->group(array(
                'period',
                'order_status'
            ));

            $this->_getWriteAdapter()->query($select->insertFromSelect($this->getMainTable(), array_keys($columns)));

            $this->_setFlagData(Mage_Reports_Model_Flag::REPORT_ORDER_FLAG_CODE);
        } catch (Exception $e) {
            $this->_getWriteAdapter()->rollBack();
            throw $e;
        }

        $this->_getWriteAdapter()->commit();
        return $this;
    }
}

