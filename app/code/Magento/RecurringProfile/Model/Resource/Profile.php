<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringProfile\Model\Resource;

/**
 * Recurring payment profiles resource model
 */
class Profile extends \Magento\Sales\Model\Resource\AbstractResource
{
    /**
     * Initialize main table and column
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('recurring_profile', 'profile_id');

        $this->_serializableFields = array(
            'profile_vendor_info' => array(null, array()),
            'additional_info' => array(null, array()),
            'order_info' => array(null, array()),
            'order_item_info' => array(null, array()),
            'billing_address_info' => array(null, array()),
            'shipping_address_info' => array(null, array())
        );
    }

    /**
     * Return recurring profile child Orders Ids
     *
     *
     * @param \Magento\Object $object
     * @return array
     */
    public function getChildOrderIds($object)
    {
        $adapter = $this->_getReadAdapter();
        $bind = array(':profile_id' => $object->getId());
        $select = $adapter->select()
            ->from(
                array('main_table' => $this->getTable('recurring_profile_order')),
                array('order_id')
            )
            ->where('profile_id=:profile_id');

        return $adapter->fetchCol($select, $bind);
    }

    /**
     * Add order relation to recurring profile
     *
     * @param int $recurringProfileId
     * @param int $orderId
     * @return $this
     */
    public function addOrderRelation($recurringProfileId, $orderId)
    {
        $this->_getWriteAdapter()->insert(
            $this->getTable('recurring_profile_order'),
            array(
                'profile_id' => $recurringProfileId,
                'order_id' => $orderId
            )
        );
        return $this;
    }
}
