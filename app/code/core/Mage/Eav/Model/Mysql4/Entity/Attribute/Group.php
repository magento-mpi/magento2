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

    public function save(Mage_Core_Model_Abstract $object) {
        $write = $this->getConnection('write');
        $groupId = $object->getId();

        $data = array(
            'attribute_set_id' => $object->getAttributeSetId(),
            'attribute_group_name' => $object->getAttributeGroupName(),
            'sort_order' => ( $object->getSortOrder() > 0 ) ? $object->getSortOrder() : ($this->_getMaxSortOrder($object) + 1)
        );

        try {
            if( $groupId > 0 ) {
                $condition = $write->quoteInto("{$this->getMainTable()}.{$this->getIdFieldName()} = ?", $groupId);
                $write->update($this->getMainTable(), $data, $condition);
            } else {
                $write->insert($this->getMainTable(), $data);
                $object->setId($write->lastInsertId());
            }
            if( $object->getAttributes() ) {
                $insertId = $write->lastInsertId();
                foreach( $object->getAttributes() as $attribute ) {
                    if( $insertId > 0 ) {
                        $attribute->setAttributeGroupId($insertId);
                    }
                    $attribute->save();
                }
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        return $this;
    }

    public function delete(Mage_Core_Model_Abstract $object)
    {
        $groups = $object->getGroupsArray();
        $setId = $object->getSetId();
        $write = $this->getConnection('write');

        $condition = $write->quoteInto("{$this->getTable('entity_attribute')}.attribute_group_id = ?", $object->getId());
        $write->update($this->getTable('entity_attribute'), array('attribute_group_id' => 0, 'attribute_set_id' => 0), $condition);

        $condition = $write->quoteInto('attribute_group_id = ?', $object->getId());
        $write->delete($this->getMainTable(), $condition);
    }

    private function _getMaxSortOrder($object)
    {
        $read = $this->getConnection('read');
        $select = $read->select()
            ->from($this->getMainTable(), new Zend_Db_Expr("MAX(`sort_order`)"))
            ->where("{$this->getMainTable()}.attribute_set_id = ?", $object->getAttributeSetId());
        $maxOrder = $read->fetchOne($select);
        return $maxOrder;
    }
}