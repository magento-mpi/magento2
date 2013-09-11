<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customers Report collection
 *
 * @category    Magento
 * @package     Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reports\Model\Resource\Customer;

class Collection extends \Magento\Customer\Model\Resource\Customer\Collection
{
    /**
     * Add order statistics flag
     *
     * @var boolean
     */
    protected $_addOrderStatistics           = false;

    /**
     * Add order statistics is filter flag
     *
     * @var boolean
     */
    protected $_addOrderStatFilter   = false;

    /**
     * Customer id table name
     *
     * @var string
     */
    protected $_customerIdTableName;

    /**
     * Customer id field name
     *
     * @var string
     */
    protected $_customerIdFieldName;

    /**
     * Order entity table name
     *
     * @var string
     */
    protected $_orderEntityTable;

    /**
     * Order entity field name
     *
     * @var string
     */
    protected $_orderEntityField;

    /**
     * Add cart info to collection
     *
     * @return \Magento\Reports\Model\Resource\Customer\Collection
     */
    public function addCartInfo()
    {
        foreach ($this->getItems() as $item) {
            $quote = \Mage::getModel('Magento\Sales\Model\Quote')->loadByCustomer($item->getId());

            if ($quote instanceof \Magento\Sales\Model\Quote) {
                $totals = $quote->getTotals();
                $item->setTotal($totals['subtotal']->getValue());
                $quoteItems = \Mage::getResourceModel('Magento\Sales\Model\Resource\Quote\Item\Collection')
                    ->setQuoteFilter($quote->getId());
                $quoteItems->load();
                $item->setItems($quoteItems->count());
            } else {
                $item->remove();
            }

        }
        return $this;
    }

    /**
     * Add customer name to results
     *
     * @return \Magento\Reports\Model\Resource\Customer\Collection
     */
    public function addCustomerName()
    {
        $this->addNameToSelect();
        return $this;
    }

    /**
     * Order for each customer
     *
     * @param string $fromDate
     * @param string $toDate
     * @return \Magento\Reports\Model\Resource\Customer\Collection
     */
    public function joinOrders($fromDate = '', $toDate = '')
    {
        if ($fromDate != '' && $toDate != '') {
            $dateFilter = " AND orders.created_at BETWEEN '{$fromDate}' AND '{$toDate}'";
        } else {
            $dateFilter = '';
        }

        $this->getSelect()
            ->joinLeft(array('orders' => $this->getTable('sales_flat_order')),
                "orders.customer_id = e.entity_id".$dateFilter,
            array());

        return $this;
    }

    /**
     * Add orders count
     *
     * @return \Magento\Reports\Model\Resource\Customer\Collection
     */
    public function addOrdersCount()
    {
        $this->getSelect()
            ->columns(array("orders_count" => "COUNT(orders.entity_id)"))
            ->where('orders.state <> ?', \Magento\Sales\Model\Order::STATE_CANCELED)
            ->group("e.entity_id");

        return $this;
    }

    /**
     * Order summary info for each customer
     * such as orders_count, orders_avg_amount, orders_total_amount
     *
     * @param int $storeId
     * @return \Magento\Reports\Model\Resource\Customer\Collection
     */
    public function addSumAvgTotals($storeId = 0)
    {
        $adapter = $this->getConnection();
        $baseSubtotalRefunded   = $adapter->getIfNullSql('orders.base_subtotal_refunded', 0);
        $baseSubtotalCanceled   = $adapter->getIfNullSql('orders.base_subtotal_canceled', 0);

        /**
         * calculate average and total amount
         */
        $expr = ($storeId == 0)
            ? "(orders.base_subtotal - {$baseSubtotalCanceled} - {$baseSubtotalRefunded}) * orders.base_to_global_rate"
            : "orders.base_subtotal - {$baseSubtotalCanceled} - {$baseSubtotalRefunded}";

        $this->getSelect()
            ->columns(array("orders_avg_amount" => "AVG({$expr})"))
            ->columns(array("orders_sum_amount" => "SUM({$expr})"));

        return $this;
    }

    /**
     * Order by total amount
     *
     * @param string $dir
     * @return \Magento\Reports\Model\Resource\Customer\Collection
     */
    public function orderByTotalAmount($dir = self::SORT_ORDER_DESC)
    {
        $this->getSelect()
            ->order("orders_sum_amount {$dir}");
        return $this;
    }

    /**
     * Add order statistics
     *
     * @param boolean $isFilter
     * @return \Magento\Reports\Model\Resource\Customer\Collection
     */
    public function addOrdersStatistics($isFilter = false)
    {
        $this->_addOrderStatistics          = true;
        $this->_addOrderStatFilter  = (bool)$isFilter;
        return $this;
    }

    /**
     * Add orders statistics to collection items
     *
     * @return \Magento\Reports\Model\Resource\Customer\Collection
     */
    protected function _addOrdersStatistics()
    {
        $customerIds = $this->getColumnValues($this->getResource()->getIdFieldName());

        if ($this->_addOrderStatistics && !empty($customerIds)) {
            $adapter = $this->getConnection();
            $baseSubtotalRefunded   = $adapter->getIfNullSql('orders.base_subtotal_refunded', 0);
            $baseSubtotalCanceled   = $adapter->getIfNullSql('orders.base_subtotal_canceled', 0);

            $totalExpr = ($this->_addOrderStatFilter)
                ? "(orders.base_subtotal-{$baseSubtotalCanceled}-{$baseSubtotalRefunded})*orders.base_to_global_rate"
                : "orders.base_subtotal-{$baseSubtotalCanceled}-{$baseSubtotalRefunded}";

            $select = $this->getConnection()->select();
            $select->from(array('orders'=>$this->getTable('sales_flat_order')), array(
                'orders_avg_amount' => "AVG({$totalExpr})",
                'orders_sum_amount' => "SUM({$totalExpr})",
                'orders_count' => 'COUNT(orders.entity_id)',
                'customer_id'
            ))->where('orders.state <> ?', \Magento\Sales\Model\Order::STATE_CANCELED)
              ->where('orders.customer_id IN(?)', $customerIds)
              ->group('orders.customer_id');

            foreach ($this->getConnection()->fetchAll($select) as $ordersInfo) {
                $this->getItemById($ordersInfo['customer_id'])->addData($ordersInfo);
            }
        }

        return $this;
    }

    /**
     * Collection after load operations like adding orders statistics
     *
     * @return \Magento\Reports\Model\Resource\Customer\Collection
     */
    protected function _afterLoad()
    {
        $this->_addOrdersStatistics();
        return $this;
    }

    /**
     * Order by customer registration
     *
     * @param string $dir
     * @return \Magento\Reports\Model\Resource\Customer\Collection
     */
    public function orderByCustomerRegistration($dir = self::SORT_ORDER_DESC)
    {
        $this->addAttributeToSort('entity_id', $dir);
        return $this;
    }

    /**
     * Get select count sql
     *
     * @return string
     */
    public function getSelectCountSql()
    {
        $countSelect = clone $this->getSelect();
        $countSelect->reset(\Zend_Db_Select::ORDER);
        $countSelect->reset(\Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(\Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(\Zend_Db_Select::COLUMNS);
        $countSelect->reset(\Zend_Db_Select::GROUP);
        $countSelect->reset(\Zend_Db_Select::HAVING);
        $countSelect->columns("count(DISTINCT e.entity_id)");

        return $countSelect;
    }
}
