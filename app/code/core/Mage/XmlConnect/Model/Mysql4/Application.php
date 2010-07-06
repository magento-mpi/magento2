<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_XmlConnect_Model_Mysql4_Application extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('xmlconnect/application', 'application_id');
    }

    /**
     * Load application by code
     *
     * @param Mage_XmlConnect_Model_Application $application
     * @param string $code
     * @return Mage_XmlConnect_Model_Mysql4_Application
     */
    public function loadByCode(Mage_XmlConnect_Model_Application $application, $code)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), array($this->getIdFieldName()))
            ->where('code=:application_code');

        if ($id = $this->_getReadAdapter()->fetchOne($select, array('application_code' => $code))) {
            $this->load($application, $id);
        } else {
            $application->setData(array());
        }
        return $this;
    }

    /** Update Application Status field, insert data to history table
     *
     * @param int $applicationId
     *
     * @return void
     */
    public function updateApplicationStatus($applicationId, $status)
    {
        $this->_getWriteAdapter()->update(
            $this->getMainTable(),
            array('status' => $status),
            $this->_getWriteAdapter()->quoteInto($this->getIdFieldName() . '=?', $applicationId)
        );
    }

    /**
     * Processing object before save data
     * Updates app_code as Store + Device
     *
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
     * Updates app_code as Store + Device + 123 (increment).
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $appCode = $object->getCode();
        $isCodePrefixed = $object->isCodePrefixed();
        if (!$isCodePrefixed) {
            $this->_getWriteAdapter()->update(
                $this->getMainTable(),
                array('code' => $this->_getWriteAdapter()->quoteInto($appCode . $object->getId())),
                $this->_getWriteAdapter()->quoteInto($this->getIdFieldName() . '=?', $object->getId())
            );
        }
        return parent::_afterSave($object);
    }

   /**
    * Returns array of existing stores and type unique pairs
    *
    * @return array
    */
    public function getExistingStoreDeviceType() {
        $select = $this->_getWriteAdapter()->select()
            ->from($this->getMainTable(), array('store_id', 'type'))
            ->group(array('store_id', 'type'))
            ->order(array('store_id', 'type'));
        return $this->_getReadAdapter()->fetchAll($select, array('store_id', 'type'));
    }

    /**
     * Returns Application id by applicationId and storeId
     *
     * @param Mage_XmlConnect_Model_Application $application
     * @param int   $applicationId
     * @param int   $storeId
     *
     * @return string
     */
    public function getIdByStoreId(Mage_XmlConnect_Model_Application $application, $applicationId, $storeId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('xa1' => $this->getMainTable()),
                array('id' => 'xa2.'.$this->getIdFieldName()), array() )
            ->join(array('xa2' => $this->getMainTable()))
            ->where('xa1.type=xa2.type')
            ->where('xa1.'.$this->getIdFieldName().'=?', (int) $applicationId)
            ->where('xa2.store_id=?', (int) $storeId);
        return $this->_getReadAdapter()->fetchOne($select);
    }

}
