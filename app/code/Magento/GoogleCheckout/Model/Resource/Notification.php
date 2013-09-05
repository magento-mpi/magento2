<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Google Checkout resource notification model
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_GoogleCheckout_Model_Resource_Notification extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Intialize resource model.
     * Set main entity table name and primary key field name.
     *
     */
    protected function _construct()
    {
        $this->_init('googlecheckout_notification', 'serial_number');
    }

    /**
     * Return notification data by serial number
     *
     * @param string $serialNumber
     * @return array
     */
    public function getNotificationData($serialNumber)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), array('*'))
            ->where('serial_number = ?', $serialNumber);

        return $this->_getReadAdapter()->fetchRow($select);
    }

    /**
     * Start notification processing
     *
     * @param string $serialNumber
     * @return Magento_GoogleCheckout_Model_Resource_Notification
     */
    public function startProcess($serialNumber)
    {
        $data = array(
            'serial_number' => $serialNumber,
            'started_at'    => \Magento\Date::now(),
            'status'        => Magento_GoogleCheckout_Model_Notification::STATUS_INPROCESS
        );
        $this->_getWriteAdapter()->insert($this->getMainTable(), $data);
        return $this;
    }

    /**
     * Stop notification processing
     *
     * @param string $serialNumber
     * @return Magento_GoogleCheckout_Model_Resource_Notification
     */
    public function stopProcess($serialNumber)
    {
        $this->_getWriteAdapter()->update($this->getMainTable(),
            array('status' => Magento_GoogleCheckout_Model_Notification::STATUS_PROCESSED),
            array('serial_number = ?' => $serialNumber)
        );
        return $this;
    }

    /**
     * Update notification processing
     *
     * @param string $serialNumber
     * @return Magento_GoogleCheckout_Model_Resource_Notification
     */
    public function updateProcess($serialNumber)
    {
        $this->_getWriteAdapter()->update($this->getMainTable(),
            array('started_at' => \Magento\Date::now()),
            array('serial_number = ?' => $serialNumber)
        );

        return $this;
    }
}
