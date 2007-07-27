<?php

class Mage_Eav_Model_Mysql4_Entity_Attribute_Group extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('eav/attribute_group', 'attribute_group_id');
    }

    public function itemExists($object)
    {
        $read = $this->getConnection('read');
        $select = $read->select()->from($this->getMainTable())
            ->where("attribute_group_name='{$object->getAttributeGroupName()}'");
        $data = $read->fetchRow($select);
        if (!$data) {
            return false;
        }
        return true;
    }
}