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

    public function deleteGroups($object)
    {
        $groups = $object->getGroupsArray();
        $setId = $object->getSetId();
        $write = $this->getConnection('write');
        foreach( $groups as $group ) {
            $condition = $write->quoteInto("{$this->getTable('entity_attribute')}.attribute_group_id = ?", $group);
            $write->update($this->getTable('entity_attribute'), array('attribute_group_id' => 0, 'attribute_set_id' => 0), $condition);

            $condition = $write->quoteInto('attribute_group_id = ?', $group);
            $write->delete($this->getMainTable(), $condition);
        }
    }
}