<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Invoice report resource model
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Resource\Report;

class Invoiced extends \Magento\Sales\Model\Resource\Report\AbstractReport
{
    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_setResource('sales');
    }

    /**
     * Aggregate Invoiced data
     *
     * @param mixed $from
     * @param mixed $to
     * @return \Magento\Sales\Model\Resource\Report\Invoiced
     */
    public function aggregate($from = null, $to = null)
    {
        // convert input dates to UTC to be comparable with DATETIME fields in DB
        $from = $this->_dateToUtc($from);
        $to   = $this->_dateToUtc($to);

        $this->_checkDates($from, $to);
        $this->_aggregateByOrderCreatedAt($from, $to);
        $this->_aggregateByInvoiceCreatedAt($from, $to);

        $this->_setFlagData(\Magento\Reports\Model\Flag::REPORT_INVOICE_FLAG_CODE);
        return $this;
    }

    /**
     * Aggregate Invoiced data by invoice created_at as period
     *
     * @param mixed $from
     * @param mixed $to
     * @return \Magento\Sales\Model\Resource\Report\Invoiced
     * @throws \Exception
     */
    protected function _aggregateByInvoiceCreatedAt($from, $to)
    {
        $table       = $this->getTable('sales_invoiced_aggregated');
        $sourceTable = $this->getTable('sales_flat_invoice');
        $orderTable  = $this->getTable('sales_flat_order');
        $adapter     = $this->_getWriteAdapter();

        $adapter->beginTransaction();

        try {
            if ($from !== null || $to !== null) {
                $subSelect = $this->_getTableDateRangeRelatedSelect(
                    $sourceTable, $orderTable, array('order_id'=>'entity_id'),
                    'created_at', 'updated_at', $from, $to
                );
            } else {
                $subSelect = null;
            }

            $this->_clearTableByDateRange($table, $from, $to, $subSelect);
            // convert dates from UTC to current admin timezone
            $periodExpr = $adapter->getDatePartSql(
                $this->getStoreTZOffsetQuery(
                    array('source_table' => $sourceTable),
                    'source_table.created_at', $from, $to
                )
            );
            $columns = array(
                // convert dates from UTC to current admin timezone
                'period'                => $periodExpr,
                'store_id'              => 'order_table.store_id',
                'order_status'          => 'order_table.status',
                'orders_count'          => new \Zend_Db_Expr('COUNT(order_table.entity_id)'),
                'orders_invoiced'       => new \Zend_Db_Expr('COUNT(order_table.entity_id)'),
                'invoiced'              => new \Zend_Db_Expr(
                    'SUM(order_table.base_total_invoiced * order_table.base_to_global_rate)'
                ),
                'invoiced_captured'     => new \Zend_Db_Expr(
                    'SUM(order_table.base_total_paid * order_table.base_to_global_rate)'
                ),
                'invoiced_not_captured' => new \Zend_Db_Expr(
                    'SUM((order_table.base_total_invoiced - order_table.base_total_paid)'
                    . ' * order_table.base_to_global_rate)'
            ));

            $select = $adapter->select();
            $select->from(array('source_table' => $sourceTable), $columns)->joinInner(
                array('order_table' => $orderTable),
                $adapter->quoteInto(
                    'source_table.order_id = order_table.entity_id AND order_table.state <> ?',
                    \Magento\Sales\Model\Order::STATE_CANCELED
                ),
                array()
            );

            $filterSubSelect = $adapter->select();
            $filterSubSelect->from(array('filter_source_table' => $sourceTable), 'MAX(filter_source_table.entity_id)')
                ->where('filter_source_table.order_id = source_table.order_id');

            if ($subSelect !== null) {
                $select->having($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
            }

            $select->where('source_table.entity_id = (?)', new \Zend_Db_Expr($filterSubSelect));
            unset($filterSubSelect);

            $select->group(array($periodExpr, 'order_table.store_id', 'order_table.status'));

            $select->having('orders_count > 0');
            $insertQuery = $select->insertFromSelect($table, array_keys($columns));
            $adapter->query($insertQuery);
            $select->reset();

            $columns = array(
                'period'                => 'period',
                'store_id'              => new \Zend_Db_Expr(\Magento\Core\Model\Store::DEFAULT_STORE_ID),
                'order_status'          => 'order_status',
                'orders_count'          => new \Zend_Db_Expr('SUM(orders_count)'),
                'orders_invoiced'       => new \Zend_Db_Expr('SUM(orders_invoiced)'),
                'invoiced'              => new \Zend_Db_Expr('SUM(invoiced)'),
                'invoiced_captured'     => new \Zend_Db_Expr('SUM(invoiced_captured)'),
                'invoiced_not_captured' => new \Zend_Db_Expr('SUM(invoiced_not_captured)')
            );

            $select->from($table, $columns)
                ->where('store_id <> ?', \Magento\Core\Model\Store::DEFAULT_STORE_ID);

            if ($subSelect !== null) {
                $select->where($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
            }

            $select->group(array('period', 'order_status'));
            $insertQuery = $select->insertFromSelect($table, array_keys($columns));
            $adapter->query($insertQuery);
            $adapter->commit();
        } catch (\Exception $e) {
            $adapter->rollBack();
            throw $e;
        }

        return $this;
    }

    /**
     * Aggregate Invoiced data by order created_at as period
     *
     * @param mixed $from
     * @param mixed $to
     * @return \Magento\Sales\Model\Resource\Report\Invoiced
     */
    protected function _aggregateByOrderCreatedAt($from, $to)
    {
        $table       = $this->getTable('sales_invoiced_aggregated_order');
        $sourceTable = $this->getTable('sales_flat_order');
        $adapter     = $this->_getWriteAdapter();

        if ($from !== null || $to !== null) {
            $subSelect = $this->_getTableDateRangeSelect($sourceTable, 'created_at', 'updated_at', $from, $to);
        } else {
            $subSelect = null;
        }

        $this->_clearTableByDateRange($table, $from, $to, $subSelect);
        // convert dates from UTC to current admin timezone
        $periodExpr = $adapter->getDatePartSql(
            $this->getStoreTZOffsetQuery(
                $sourceTable, 'created_at', $from, $to
            )
        );

        $columns = array(
            'period'                => $periodExpr,
            'store_id'              => 'store_id',
            'order_status'          => 'status',
            'orders_count'          => new \Zend_Db_Expr('COUNT(base_total_invoiced)'),
            'orders_invoiced'       => new \Zend_Db_Expr(
                sprintf('SUM(%s)', $adapter->getCheckSql('base_total_invoiced > 0', 1, 0))
            ),
            'invoiced'              => new \Zend_Db_Expr(
                sprintf(
                    'SUM(%s * %s)',
                    $adapter->getIfNullSql('base_total_invoiced', 0),
                    $adapter->getIfNullSql('base_to_global_rate', 0)
                )
            ),
            'invoiced_captured'     => new \Zend_Db_Expr(
                sprintf(
                    'SUM(%s * %s)',
                    $adapter->getIfNullSql('base_total_paid', 0),
                    $adapter->getIfNullSql('base_to_global_rate', 0)
                )
            ),
            'invoiced_not_captured' => new \Zend_Db_Expr(
                sprintf(
                    'SUM((%s - %s) * %s)',
                    $adapter->getIfNullSql('base_total_invoiced', 0),
                    $adapter->getIfNullSql('base_total_paid', 0),
                    $adapter->getIfNullSql('base_to_global_rate', 0)
                )
            )
        );

        $select = $adapter->select();
        $select->from($sourceTable, $columns)->where('state <> ?', \Magento\Sales\Model\Order::STATE_CANCELED);

        if ($subSelect !== null) {
            $select->having($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
        }

        $select->group(array($periodExpr, 'store_id', 'status'));
        $select->having('orders_count > 0');

        $insertQuery = $select->insertFromSelect($table, array_keys($columns));
        $adapter->query($insertQuery);
        $select->reset();

        $columns = array(
            'period'                => 'period',
            'store_id'              => new \Zend_Db_Expr(\Magento\Core\Model\Store::DEFAULT_STORE_ID),
            'order_status'          => 'order_status',
            'orders_count'          => new \Zend_Db_Expr('SUM(orders_count)'),
            'orders_invoiced'       => new \Zend_Db_Expr('SUM(orders_invoiced)'),
            'invoiced'              => new \Zend_Db_Expr('SUM(invoiced)'),
            'invoiced_captured'     => new \Zend_Db_Expr('SUM(invoiced_captured)'),
            'invoiced_not_captured' => new \Zend_Db_Expr('SUM(invoiced_not_captured)')
        );

        $select->from($table, $columns)->where('store_id <> ?', \Magento\Core\Model\Store::DEFAULT_STORE_ID);

        if ($subSelect !== null) {
            $select->where($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
        }

        $select->group(array('period', 'order_status'));
        $insertQuery = $select->insertFromSelect($table, array_keys($columns));
        $adapter->query($insertQuery);
        return $this;
    }
}
