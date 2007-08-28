<?php
class Mage_Permissions_Model_Mysql4_Role extends Mage_Core_Model_Mysql4_Abstract
{

    protected function _construct()
    {
        $this->_init('permissions/role', 'role_id');
    }

    /**
     *
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (! $object->getId()) {
            $object->setCreated(now());
        }
        $object->setModified(now());
        return $this;
    }

    public function load(Mage_Core_Model_Abstract $object, $value, $field=null)
    {
        if (!intval($value) && is_string($value)) {
            $field = 'role_id';
        }
        return parent::load($object, $value, $field);
    }
}
