<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Rule report resource model with aggregation by created at
 *
 * @category    Mage
 * @package     Mage_SalesRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_SalesRule_Model_Resource_Report_Rule_Createdat extends Mage_Reports_Model_Resource_Report_Abstract
{
    /**
     * Resource Report Rule constructor
     *
     */
    protected function _construct()
    {
        $this->_init('coupon_aggregated', 'id');
    }

    /**
     * Aggregate Coupons data by order created at
     *
     * @param mixed $from
     * @param mixed $to
     * @return Mage_SalesRule_Model_Resource_Report_Rule_Createdat
     */
    public function aggregate($from = null, $to = null)
    {
        return $this->_aggregateByOrder('created_at', $from, $to);
    }

    /**
     * Aggregate coupons reports by orders
     *
     * @throws Exception
     * @param string $aggregationField
     * @param mixed $from
     * @param mixed $to
     * @return Mage_SalesRule_Model_Resource_Report_Rule_Createdat
     */
    protected function _aggregateByOrder($aggregationField, $from, $to)
    {
        $table = $this->getMainTable();
        $sourceTable = $this->getTable('sales_flat_order');
        $adapter = $this->_getWriteAdapter();
        $adapter->beginTransaction();

        try {
            if ($from !== null || $to !== null) {
                $subSelect = $this->_getTableDateRangeSelect($sourceTable, 'created_at', 'updated_at', $from, $to);
            } else {
                $subSelect = null;
            }

            $this->_clearTableByDateRange($table, $from, $to, $subSelect);

            // convert dates from UTC to current admin timezone
            $periodExpr = $adapter->getDatePartSql(
                $this->getStoreTZOffsetQuery($sourceTable, $aggregationField, $from, $to)
            );

            $columns = array(
                'period'                  => $periodExpr,
                'store_id'                => 'store_id',
                'order_status'            => 'status',
                'coupon_code'             => 'coupon_code',
                'coupon_uses'             => 'COUNT(entity_id)',

                'subtotal_amount'         =>
                    $adapter->getIfNullSql('SUM((base_subtotal - ' .
                        $adapter->getIfNullSql('base_subtotal_canceled', 0).') * base_to_global_rate)', 0),

                'discount_amount'         =>
                    $adapter->getIfNullSql('SUM((ABS(base_discount_amount) - ' .
                        $adapter->getIfNullSql('base_discount_canceled', 0).') * base_to_global_rate)', 0),

                'total_amount'            =>
                    $adapter->getIfNullSql('SUM((base_subtotal - ' .
                        $adapter->getIfNullSql('base_subtotal_canceled', 0) . ' - '.
                        $adapter->getIfNullSql('ABS(base_discount_amount) - ' .
                        $adapter->getIfNullSql('base_discount_canceled', 0), 0). ')
                        * base_to_global_rate)', 0),

                'subtotal_amount_actual'  =>
                    $adapter->getIfNullSql('SUM((base_subtotal_invoiced - ' .
                        $adapter->getIfNullSql('base_subtotal_refunded', 0). ') * base_to_global_rate)', 0),

                'discount_amount_actual'  =>
                    $adapter->getIfNullSql('SUM((base_discount_invoiced - ' .
                        $adapter->getIfNullSql('base_discount_refunded', 0) . ')
                        * base_to_global_rate)', 0),

                'total_amount_actual'     =>
                    $adapter->getIfNullSql('SUM((base_subtotal_invoiced - ' .
                        $adapter->getIfNullSql('base_subtotal_refunded', 0) . ' - ' .
                        $adapter->getIfNullSql('base_discount_invoiced - ' .
                        $adapter->getIfNullSql('base_discount_refunded', 0), 0) .
                        ') * base_to_global_rate)', 0),
            );

            $select = $adapter->select();
            $select->from(array('source_table' => $sourceTable), $columns)
                 ->where('coupon_code IS NOT NULL');

            if ($subSelect !== null) {
                $select->having($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
            }

            $select->group(array(
                $periodExpr,
                'store_id',
                'status',
                'coupon_code'
            ));

            $select->having('COUNT(entity_id) > 0');
            $select->insertFromSelect($table, array_keys($columns));

            $adapter->query($select->insertFromSelect($table, array_keys($columns)));

            $select->reset();

            $columns = array(
                'period'                  => 'period',
                'store_id'                => new Zend_Db_Expr('0'),
                'order_status'            => 'order_status',
                'coupon_code'             => 'coupon_code',
                'coupon_uses'             => 'SUM(coupon_uses)',
                'subtotal_amount'         => 'SUM(subtotal_amount)',
                'discount_amount'         => 'SUM(discount_amount)',
                'total_amount'            => 'SUM(total_amount)',
                'subtotal_amount_actual'  => 'SUM(subtotal_amount_actual)',
                'discount_amount_actual'  => 'SUM(discount_amount_actual)',
                'total_amount_actual'     => 'SUM(total_amount_actual)',
            );

            $select
                ->from($table, $columns)
                ->where('store_id <> 0');

            if ($subSelect !== null) {
                $select->where($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
            }

            $select->group(array(
                'period',
                'order_status',
                'coupon_code'
            ));

            $adapter->query($select->insertFromSelect($table, array_keys($columns)));
            $adapter->commit();
        } catch (Exception $e) {
            $adapter->rollBack();
            throw $e;
        }

        return $this;
    }
}
