<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Model\Resource;

use \Magento\Framework\Model\Resource\Db\AbstractDb;

/**
 * Recurring payment resource model
 */
class Payment extends AbstractDb
{
    /**
     * Initialize main table and column
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('recurring_payment', 'payment_id');

        $this->_serializableFields = array(
            'payment_vendor_info' => array(null, array()),
            'additional_info' => array(null, array()),
            'order_info' => array(null, array()),
            'order_item_info' => array(null, array()),
            'billing_address_info' => array(null, array()),
            'shipping_address_info' => array(null, array())
        );
    }

    /**
     * Return recurring payment child Orders Ids
     *
     *
     * @param \Magento\Framework\Object $object
     * @return array
     */
    public function getChildOrderIds($object)
    {
        $adapter = $this->_getReadAdapter();
        $bind = array(':payment_id' => $object->getId());
        $select = $adapter->select()->from(
            array('main_table' => $this->getTable('recurring_payment_order')),
            array('order_id')
        )->where(
            'payment_id=:payment_id'
        );

        return $adapter->fetchCol($select, $bind);
    }

    /**
     * Add order relation to recurring payment
     *
     * @param int $recurringPaymentId
     * @param int $orderId
     * @return $this
     */
    public function addOrderRelation($recurringPaymentId, $orderId)
    {
        $this->_getWriteAdapter()->insert(
            $this->getTable('recurring_payment_order'),
            array('payment_id' => $recurringPaymentId, 'order_id' => $orderId)
        );
        return $this;
    }
}
