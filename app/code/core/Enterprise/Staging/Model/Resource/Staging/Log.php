<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Staging log resource module
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Model_Resource_Staging_Log extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('enterprise_staging_log', 'log_id');
    }

    /**
     * Prepare some data before save processing
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Enterprise_Staging_Model_Resource_Staging_Event
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getId()) {
            $object->setIsNew(true);
            $value = Mage::getModel('Mage_Core_Model_Date')->gmtDate();
            $object->setCreatedAt($value);
        }

        $user = Mage::getSingleton('Mage_Backend_Model_Auth_Session')->getUser();
        if ($user) {
            $object->setUserId($user->getId());
            $object->setUsername($user->getName());
        } else {
            $object->setUsername('CRON');
        }

        $object->setIp(Mage::helper('Mage_Core_Helper_Http')->getRemoteAddr(true));

        return parent::_beforeSave($object);
    }

    /**
     * Retrieve action of last log by staging id
     *
     * @param int $stagingId
     * @return int
     */
    public function getLastLogAction($stagingId)
    {
        $adapter = $this->_getReadAdapter();
        if ($stagingId) {
            $select = $adapter->select()
                ->from(array('main_table' => $this->getMainTable()), array('action'))
                ->where('main_table.staging_id=?', $stagingId)
                ->order('log_id DESC');
            return $adapter->fetchOne($select);
        }
        return false;
    }
}
