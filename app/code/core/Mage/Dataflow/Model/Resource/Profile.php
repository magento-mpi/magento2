<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Dataflow
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Convert profile resource model
 *
 * @category    Mage
 * @package     Mage_Dataflow
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Dataflow_Model_Resource_Profile extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_init('dataflow_profile', 'profile_id');
    }

    /**
     * Setting up created_at and updarted_at
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getCreatedAt()) {
            $object->setCreatedAt($this->formatDate(time()));
        }
        $object->setUpdatedAt($this->formatDate(time()));
        parent::_beforeSave($object);
    }

    /**
     * Returns true if profile with name exists
     *
     * @param string $name
     * @param int $id
     * @return bool
     */
    public function isProfileExists($name, $id = null)
    {
        $bind = array('name' => $name);
        $select = $this->_getReadAdapter()->select();
        $select
            ->from($this->getMainTable(), 'count(1)')
            ->where('name = :name');
        if ($id) {
            $select->where("{$this->getIdFieldName()} != :id");
            $bind['id'] = $id;
        }
        $result = $this->_getReadAdapter()->fetchOne($select, $bind) ? true : false;
        return $result;
    }
}
