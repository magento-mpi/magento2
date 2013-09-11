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
namespace Magento\GoogleCheckout\Model\Resource;

class Notification extends \Magento\Core\Model\Resource\Db\AbstractDb
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
     * @return \Magento\GoogleCheckout\Model\Resource\Notification
     */
    public function startProcess($serialNumber)
    {
        $data = array(
            'serial_number' => $serialNumber,
            'started_at'    => \Magento\Date::now(),
            'status'        => \Magento\GoogleCheckout\Model\Notification::STATUS_INPROCESS
        );
        $this->_getWriteAdapter()->insert($this->getMainTable(), $data);
        return $this;
    }

    /**
     * Stop notification processing
     *
     * @param string $serialNumber
     * @return \Magento\GoogleCheckout\Model\Resource\Notification
     */
    public function stopProcess($serialNumber)
    {
        $this->_getWriteAdapter()->update($this->getMainTable(),
            array('status' => \Magento\GoogleCheckout\Model\Notification::STATUS_PROCESSED),
            array('serial_number = ?' => $serialNumber)
        );
        return $this;
    }

    /**
     * Update notification processing
     *
     * @param string $serialNumber
     * @return \Magento\GoogleCheckout\Model\Resource\Notification
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
