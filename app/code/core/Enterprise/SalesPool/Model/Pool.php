<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category    Enterprise
 * @package     Enterprise_SalesPool
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Pool model
 *
 */
class Enterprise_SalesPool_Model_Pool extends Mage_Core_Model_Abstract
{
     /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'enterprise_salespool_pool';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'pool';

    protected function _construct()
    {
        $this->_init('enterprise_salespool/pool');
    }

    /**
     * Retrieve salespool flag
     *
     * @return Enterprise_SalesPool_Model_Flag
     */
    protected function _getFlag()
    {
        return Mage::getSingleton('enterprise_salespool/flag');
    }

    /**
     * Retrieve pool configuration
     *
     * @return Enterprise_SalesPool_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('enterprise_salespool/config');
    }

    /**
     * Updates autoincrement for one of entities
     *
     * @param string $entity
     * @param string $primaryColumn
     * @param int|null $newIncrement
     * @return int
     */
    public function updateAutoincrement($entity, $primaryColumn = null, $newIncrement = null)
    {
        if ($primaryColumn === null) {
            $entityConfig = $this->_getConfig()->getPoolEntity($entity);
            if (isset($entityConfig['primary'])) {
                $primaryColumn = $entityConfig['primary'];
            }
        }
        return $this->getResource()->updateAutoincrement($entity, $primaryColumn, $newIncrement);
    }

    /**
     * Synchronize autoincrement for pool entities
     *
     * @return Enterprise_SalesPool_Model_Pool
     */
    public function syncAutoincrement()
    {
        $autoIncrements = array();

        foreach ($this->_getConfig()->getPoolEntities() as $entityCode => $entityConfig) {
            $autoIncrements[$entityCode] = $this->updateAutoincrement($entityCode);
        }

        Mage::dispatchEvent($this->_eventPrefix . '_sync_autoincrement', array($this->_eventObject, $autoIncrements));

        return $this;
    }

    /**
     * Flush all orders from pool
     *
     * @return Enterprise_SalesPool_Model_Pool
     */
    public function flushAllOrders()
    {
        $ordersToFlush = $this->getResource()->getOrderIdsForFlush();

        if (!empty($ordersToFlush)) {
            $this->_getResource()->beginTransaction();
            try {
                $this->_flushOrders($ordersToFlush, true);
                $this->_getResource()->commit();
            } catch (Exception $e) {
                $this->_getResource()->rollBack();
                throw $e;
            }
        }

        return $this;
    }

    /**
     * Flush order(s) from pool by id(s)
     *
     * @param int|array $orderId
     * @return int Count of flushed orders
     */
    public function flushOrderById($orderId)
    {
        if (!is_array($orderId)) {
            $orderId = array($orderId);
        }

        $ordersToFlush = $this->getResource()->getOrderIdsForFlushByIds($orderId);

        if (!empty($ordersToFlush)) {
            $this->_getResource()->beginTransaction();
            try {
                $this->_flushOrders($ordersToFlush);
                $this->_getResource()->commit();
            } catch (Exception $e) {
                $this->_getResource()->rollBack();
                throw $e;
            }
        }

        return count($ordersToFlush);
    }

    /**
     * Flush orders from pool based on incoming parameters
     *
     * @param array $ordersToFlush
     * @param boolean $limit
     * @return Enterprise_SalesPool_Model_Pool
     */
    protected function _flushOrders($ordersToFlush, $limit = false)
    {
        $ordersWithInvoice = array();
        $orderIds = array_keys($ordersToFlush);

        foreach ($ordersToFlush as $orderId => $createInvoice) {
            if ($createInvoice) {
                $ordersWithInvoice[] = $orderId;
            }
        }

        $invoicesData = array();
        if (!empty($ordersWithInvoice)) {
            $invoicesData = $this->getResource()->getInvoicesData($ordersWithInvoice);
        }

        if ($limit) {
            $limitOrders = max($orderIds);
        } else {
            $limitOrders = &$orderIds;
        }

        $this->getResource()->movePoolRecords('order', $limitOrders, null, $limit);
        $this->getResource()->movePoolRecords('order_item', $limitOrders, 'order_id', $limit);
        $this->getResource()->movePoolRecords('order_address', $limitOrders, 'parent_id', $limit);
        $this->getResource()->movePoolRecords('order_payment', $limitOrders, 'parent_id', $limit);
        $this->getResource()->movePoolRecords('order_payment_transaction', $limitOrders, 'order_id', $limit);
        $this->getResource()->movePoolRecords('order_status_history', $limitOrders, 'parent_id', $limit);

        Mage::dispatchEvent($this->_eventPrefix . '_flush_orders', array(
            'order_ids'             => $orderIds,
            'invoices_data'         => $invoicesData,
            'orders_with_invoice'   => $ordersWithInvoice,
            $this->_eventObject     => $this
        ));

        Mage::getResourceSingleton('sales/order')->updateGridRecords($orderIds);

        $invoice = Mage::getModel('sales/order_invoice');
        $invoiceIds = array();

        foreach ($invoicesData as $orderId => $invoiceData) {
            $invoice->reset();
            $invoice->setIsFlushProcess(true);
            Mage::getSingleton('enterprise_salespool/pool_invoice')->restoreFromOrder($orderId, $invoice, $invoiceData);
            $invoice->save();
            $invoiceIds[] = $invoice->getId();
        }

        if (!empty($invoiceIds)) {
            Mage::getResourceSingleton('sales/order_invoice')->updateGridRecords($invoiceIds);
        }

        return $this;
    }

    /**
     * Patch object for pool (set pool as main table)
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Enterprise_SalesPool_Model_Pool
     */
    public function applyPatchForObject($object)
    {
        $this->getResource()->applyPatchForObject($object);
        return $this;
    }

    /**
     * Apply patch to collection for pool (set pool as main table)
     *
     * @param Mage_Core_Model_Mysql4_Collection_Abstract $object
     * @return Enterprise_SalesPool_Model_Pool
     */
    public function applyPatchForCollection($collection)
    {
        $this->getResource()->applyPatchForCollection($collection);
        return $this;
    }

    /**
     * Discard patch object for pool (restore main table)
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Enterprise_SalesPool_Model_Pool
     */
    public function discardPatchForObject($object)
    {
        $this->getResource()->discardPatchForObject($object);
        return $this;
    }

    /**
     * Handle update grid rows for entities
     *
     * @param Mage_Sales_Model_Mysql4_Abstract $resource
     * @param array $ids
     * @return array
     */
    public function handleUpdateGridRecords($resource, $ids)
    {
        return $this->getResource()->handleUpdateGridRecords($resource, $ids);
    }

    /**
     * Mark order for creating of invoice
     *
     * @param Mage_Sales_Model_Order $order
     * @return Enterprise_SalesPool_Model_Pool
     */
    public function markOrderForInvoice(Mage_Sales_Model_Order $order)
    {
        $this->getResource()->markOrderForInvoice($order);
        return $this;
    }

}
