<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * XmlConnect Model Resource Application
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Resource_Application extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Constructor, setting table and index field
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init('xmlconnect_application', 'application_id');
    }

    /**
     * Update Application Status field, insert data to history table
     *
     * @param int $applicationId
     * @param string $status
     * @return Mage_XmlConnect_Model_Resource_Application
     */
    public function updateApplicationStatus($applicationId, $status)
    {
        $this->_getWriteAdapter()->update(
            $this->getMainTable(),
            array('status' => $status),
            $this->_getWriteAdapter()->quoteInto($this->getIdFieldName() . '=?', $applicationId)
        );
        return $this;
    }

    /**
     * Processing object before save data
     * Update app_code as Store + Device
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getId()) {
            $object->setCode($object->getCodePrefix());
        }
        return parent::_beforeSave($object);
    }

    /**
     * Processing object after save data
     * Update app_code as Store + Device + 123 (increment).
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $appCode = $object->getCode();
        $isCodePrefixed = $object->isCodePrefixed();
        if (!$isCodePrefixed) {
            $this->_getWriteAdapter()->update(
                $this->getMainTable(),
                array('code' => $appCode . $object->getId()),
                $this->_getWriteAdapter()->quoteInto($this->getIdFieldName() . '=?', $object->getId())
            );
        }
        return parent::_afterSave($object);
    }

    /**
     * Collect existing stores and type unique pairs
     *
     * @return array
     */
    public function getExistingStoreDeviceType()
    {
        $select = $this->_getWriteAdapter()->select()->from($this->getMainTable(), array('store_id', 'type'))
            ->group(array('store_id', 'type'))->order(array('store_id', 'type'));
        return $this->_getReadAdapter()->fetchAll($select, array('store_id', 'type'));
    }

    /**
     * Update all applications "updated at" parameter with current date
     *
     * @return Mage_XmlConnect_Model_Resource_Application
     */
    public function updateAllAppsUpdatedAtParameter()
    {
        $this->_getWriteAdapter()->update($this->getMainTable(), array('updated_at' => date('Y-m-d H:i:s')));
        return $this;
    }
}
