<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customerbalance history resource model
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerBalance
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_CustomerBalance_Model_Resource_Balance_History extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('enterprise_customerbalance_history', 'history_id');
    }

    /**
     * Set updated_at automatically before saving
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Enterprise_CustomerBalance_Model_Resource_Balance_History
     */
    public function _beforeSave(Magento_Core_Model_Abstract $object)
    {
        $object->setUpdatedAt($this->formatDate(time()));
        return parent::_beforeSave($object);
    }

    /**
     * Mark specified balance history record as sent to customer
     *
     * @param int $id
     */
    public function markAsSent($id)
    {
        $this->_getWriteAdapter()->update($this->getMainTable(),
            array('is_customer_notified' => 1),
            array('history_id = ?' => $id)
        );
    }
}
