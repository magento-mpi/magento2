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
 * Pool observer
 *
 */
class Enterprise_SalesPool_Model_Observer
{
    /**
     * Pool configuration object instance
     *
     * @var Enterprise_SalesPool_Model_Config
     */
    protected $_config;

    /**
     * Pool model instance
     * @var Enterprise_SalesPool_Model_Pool
     */
    protected $_pool;

    /**
     * Map pool entity type to specific model class name
     *
     * @var array
     */
    protected $_entityToClass = array(
        'order'                 => 'sales/order',
        'order_payment'         => 'sales/order_payment',
        'order_address'         => 'sales/order_address',
        'order_item'            => 'sales/order_item',
        'order_status_history'  => 'sales/order_status_history',
        'order_payment_transaction' => 'sales/order_payment_transaction',
    );

    /**
     * Pool flag object instance
     *
     * @var Enterprise_SalesPool_Model_Flag
     */
    protected $_flag = null;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_config  = Mage::getSingleton('enterprise_salespool/config');
        $this->_pool    = Mage::getModel('enterprise_salespool/pool');
    }

    /**
     * Retrieve salespool flag
     *
     * @return Enterprise_SalesPool_Model_Flag
     */
    protected function _getFlag()
    {
        if ($this->_flag === null) {
            $this->_flag = Mage::getSingleton('enterprise_salespool/flag');
            $this->_flag->loadSelf();
        }

        return $this->_flag;
    }

    /**
     * Detect entity type from object instance
     *
     * @param Mage_Core_Model_Abstract $object
     * @return string|boolean
     */
    protected function _detectEntity($object)
    {
        foreach ($this->_entityToClass as $entityCode => $className) {
            $className = Mage::getConfig()->getModelClassName($className);
            if ($object instanceof $className) {
                return $entityCode;
            }
        }

        return false;
    }

    /**
     * Flush pool orders by cron
     *
     * @return Enterprise_SalesPool_Model_Observer
     */
    public function flushOrdersByCron()
    {
        if ($this->_config->isPoolActive()) {
            if ($this->_config->getPoolFlushPeriod() < (time() - $this->_getFlag()->getLastFlushTime())) {
                $this->_pool->flushAllOrders();
            }
        }
        return $this;
    }

    /**
     * Patch sales object load for pool
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_SalesPool_Model_Observer
     */
    public function salesObjectBeforeLoad(Varien_Event_Observer $observer)
    {
        if (!$this->_config->isPoolActive()) {
            return $this;
        }

        $object = $observer->getEvent()->getDataObject();
        $field = $observer->getEvent()->getField();
        $value = $observer->getEvent()->getValue();

        if ($object->getLoadFromPool()) {
            $this->_pool->applyPatchForObject($object);
        } else {
            $object->setTryLoadFromPool(array($field, $value));
        }

        return $this;
    }

    /**
     * Patch order load for pool on afterload
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_SalesPool_Model_Observer
     */
    public function salesObjectAfterLoad(Varien_Event_Observer $observer)
    {
        if (!$this->_config->isPoolActive()) {
            return $this;
        }

        $object = $observer->getEvent()->getOrder();

        if (!$object->getId() && $object->getTryLoadFromPool()) { // Trying to load order from pool on fault
            list($field, $value) = $object->getTryLoadFromPool();
            $object->unsTryLoadFromPool();
            $object->setLoadFromPool(true);
            $object->load($value, $field);
            return $this;
        }

        if ($object->getInPool() || $object->getPoolPatched()) {
            $this->_pool->discardPatchForObject($object);
            if ($object instanceof Mage_Sales_Model_Order) {
                $this->_limitOrderActions($object);
            }
        }

        return $this;
    }

    /**
     * Apply order limitations related with pool
     * We cannot create invoices, shipments, creditmemos, etc in pool mode
     *
     * @param Mage_Sales_Model_Order $order
     * @return Enterprise_SalesPool_Model_Observer
     */
    protected function _limitOrderActions($order)
    {
        $order->setForceUpdateGridRecords(true);
        $order->setActionFlag(Mage_Sales_Model_Order::ACTION_FLAG_INVOICE, false);
        $order->setActionFlag(Mage_Sales_Model_Order::ACTION_FLAG_SHIP, false);
        $order->setActionFlag(Mage_Sales_Model_Order::ACTION_FLAG_CREDITMEMO, false);
        $order->setActionFlag(Mage_Sales_Model_Order::ACTION_FLAG_CANCEL, false);
        $order->setActionFlag(Mage_Sales_Model_Order::ACTION_FLAG_HOLD, false);
        $order->setActionFlag(Mage_Sales_Model_Order::ACTION_FLAG_UNHOLD, false);
        $order->setActionFlag(Mage_Sales_Model_Order::ACTION_FLAG_EDIT, false);
        $order->setActionFlag(Mage_Sales_Model_Order::ACTION_FLAG_REORDER, false);
        $order->setBackUrl(
            Mage::helper('adminhtml')->getUrl('adminhtml/sales_order_pool/')
        );
        $order->setIsMoveable(false);
        return $this;
    }

    /**
     * Patches transaction object for loading from pool if order already in pool
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_SalesPool_Model_Observer
     */
    public function salesOrderPaymentTransactionBeforeLoadByTxnId(Varien_Event_Observer $observer)
    {
        if (!$this->_config->isPoolActive()) {
            return $this;
        }

        $object = $observer->getEvent()->getDataObject();

        if ($object->getOrder() && $object->getOrder()->getInPool()) {
            $this->_pool->applyPatchForObject($object);
        }
        return $this;
    }

    /**
     * Discard changes made to order payment transaction before its load
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_SalesPool_Model_Observer
     */
    public function salesOrderPaymentTransactionAfterLoadByTxnId(Varien_Event_Observer $observer)
    {
        if (!$this->_config->isPoolActive()) {
            return $this;
        }

        $object = $observer->getEvent()->getDataObject();

        if ($object->getOrder() && $object->getOrder()->getInPool()) {
            $this->_pool->discardPatchForObject($object);
        }
        return $this;
    }


    /**
     * Patches order related collection if order in pool
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_SalesPool_Model_Observer
     */
    public function salesCollectionSetSalesOrder(Varien_Event_Observer $observer)
    {
        if (!$this->_config->isPoolActive()) {
            return $this;
        }

        $collection = $observer->getEvent()->getCollection();
        if ($collection->getSalesOrder()->getInPool()) {
            $this->_pool->applyPatchForCollection($collection);
        }

        return $this;
    }

    /**
     * Patch sales object before save
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_SalesPool_Model_Observer
     */
    public function salesObjectBeforeSave(Varien_Event_Observer $observer)
    {
        $object = $observer->getEvent()->getDataObject();
        $entity = $this->_detectEntity($object);

        if (!$this->_config->isPoolActive() || !$entity) {
            return $this;
        }

        $isOrderInPool = $object->getOrder() && $object->getOrder()->getInPool();
        $isOrderNew = !$object->getId() && ($object instanceof Mage_Sales_Model_Order);

        if ($object->hasDataChanges() && ($isOrderNew || $isOrderInPool || $object->getPoolPatched())) {
            if ($this->_config->isCurrentlyPoolActive($object) || $isOrderInPool || $object->getPoolPatched()) { // Pool active in current store
                $this->_pool->applyPatchForObject($object);
                $object->setForceUpdateGridRecords(true);
                if ($isOrderNew) {
                    $object->setInPool(1);
                    $shipping = $object->getShippingAddress();
                    if ($shipping) {
                        $object->setShippingName($shipping->getFirstname() . ' ' . $shipping->getLastname());
                    }
                    $billing = $object->getBillingAddress();
                    if ($billing) {
                        $object->setBillingName($billing->getFirstname() . ' ' . $billing->getLastname());
                    }
                }
            } elseif ($this->_config->isPoolActive()) { // For admin order we keep order ids syncronized
                $newId = $this->_pool->updateAutoincrement($entity, null, false);
                $object->setId($newId);
                $object->isObjectNew(true);
            }
        }

        return $this;
    }

    /**
     * Discard applied patch for sales object on after load
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_SalesPool_Model_Observer
     */
    public function salesObjectAfterSave(Varien_Event_Observer $observer)
    {
        $object = $observer->getEvent()->getDataObject();
        $entity = $this->_detectEntity($object);
        if (!$this->_config->isPoolActive() || !$entity) {
            return $this;
        }

        if ($object->isObjectNew()) {
            $object->isObjectNew(false);
        }

        if ($this->_config->isCurrentlyPoolActive($object) || $object->getPoolPatched()) {
            $this->_pool->discardPatchForObject($object);
        }
        return $this;
    }

    /**
     * Handles order grid rows update
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_SalesPool_Model_Observer
     */
    public function salesOrderUpdateGridRecords(Varien_Event_Observer $observer)
    {
        $proxy = $observer->getEvent()->getProxy();
        $proxy->setIds(
            $this->_pool->handleUpdateGridRecords($proxy->getResource(), $proxy->getIds())
        );
        return $this;
    }

    /**
     * Before saving attribute observer
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_SalesPool_Model_Observer
     */
    public function salesOrderBeforeSaveAttribute(Varien_Event_Observer $observer)
    {
        $object = $observer->getEvent()->getObject();

        if (!$this->_config->isPoolActive()) {
            return $this;
        }

        if ($object->getInPool()) {
            $this->_pool->applyPatchForObject($object);
        }
        return $this;
    }

    /**
     * After saving attribute observer
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_SalesPool_Model_Observer
     */
    public function salesOrderAfterSaveAttribute(Varien_Event_Observer $observer)
    {
        $object = $observer->getEvent()->getObject();

        if (!$this->_config->isPoolActive()) {
            return $this;
        }

        if ($object->getPoolPatched()) {
            $this->_pool->discardPatchForObject($object);
        }
        return $this;
    }

    /**
     * Before saving order invoice check order in pool existance
     *
     * @return Enterprise_SalesPool_Model_Observer
     */
    public function salesOrderInvoiceBeforeSave(Varien_Event_Observer $observer)
    {
        if (!$this->_config->isPoolActive()) {
            return $this;
        }

        /* @var $invoice Mage_Sales_Model_Order_Invoice */
        $invoice = $observer->getEvent()->getInvoice();
        if (!$invoice->getIsFlushProcess() && $invoice->getOrder()->getInPool()) {
            Mage::getSingleton('enterprise_salespool/pool_invoice')->saveToOrder($invoice);
            $this->_pool->markOrderForInvoice($invoice->getOrder());
        }
        return $this;
    }
}
