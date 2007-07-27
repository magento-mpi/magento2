<?php

class Mage_Eav_Model_Mysql4_Entity_Attribute extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('eav/attribute', 'attribute_id');
    }

    public function loadByName($object, $entityTypeId, $name)
    {
        $read = $this->getConnection('read');

        $select = $read->select()->from($this->getMainTable())
            ->where('entity_type_id=?', $entityTypeId)
            ->where('attribute_name=?', $name);
        $data = $read->fetchRow($select);

        if (!$data) {
            return false;
        }

        $object->setData($data);

        $this->_afterLoad($object);

        return true;
    }

    public function itemExists($object)
    {
        $read = $this->getConnection('read');

        $select = $read->select()->from($this->getMainTable())
            ->where("attribute_name='{$object->getAttributeName()}' OR attribute_code='{$object->getAttributeCode()}'")
            ->where('attribute_id!=?', $object->getAttributeId());
        $data = $read->fetchRow($select);
        if (!$data) {
            return false;
        }
        return true;
    }

    public function saveAttributes($object)
    {
        $write = $this->getConnection('write');
        $write->beginTransaction();

        try {
            if( is_array($object->getNotAttributesArray()) ) {
                foreach( $object->getNotAttributesArray() as $attribute ) {
                    $condition = "attribute_id = {$attribute} AND entity_type_id = {$object->getEntityTypeId()}";
                    $write->update($this->getTable('entity_attribute'), array('attribute_set_id' => 0, 'attribute_group_id' => 0), $condition);
                }
            }

            if( is_array($object->getAttributesArray()) ) {
                foreach( $object->getAttributesArray() as $key => $attribute ) {
                    $condition = "attribute_id = {$attribute[0]} AND entity_type_id = {$object->getEntityTypeId()}";
                    $updateData = array(
                        'entity_type_id' => $object->getEntityTypeId(),
                        'attribute_set_id' => $object->getSetId(),
                        'attribute_group_id' => $attribute[1],
                        'sort_order' => $attribute[2],
                    );
                    $write->update($this->getTable('entity_attribute'), $updateData, $condition);
                }
            }

            $write->commit();
        } catch (Exception $e) {
            $write->rollback();
        }
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $data = array(
            'entity_type_id' => ( $object->getEntityTypeId() > 0 ) ? $object->getEntityTypeId() : 0,
            'attribute_set_id' => ( $object->getAttributeSetId() > 0 ) ? $object->getAttributeSetId() : 0,
            'attribute_group_id' => ( $object->getAttributeGroupId() > 0 ) ? $object->getAttributeGroupId() : 0,
            'attribute_id' => $object->getId(),
            'sort_order' => ( $this->_getMaxSortOrder($object) + 1 ),
        );

        if( intval($object->getEntityAttributeId()) == 0 ) {
            $write = $this->getConnection('write');
            $write->insert($this->getTable('entity_attribute'), $data);
        } else {
            $condition = $write->quoteInto("{$this->getTable('entity_attribute')}.{$this->getIdFieldName()} = ?", $object->getId());
            $write->update($this->getTable('entity_attribute'), $data, $condition);
        }
    }

    private function _getMaxSortOrder($object)
    {
        if( intval($object->getAttributeGroupId()) > 0 ) {
            $read = $this->getConnection('read');
            $select = $read->select()
                ->from($this->getTable('entity_attribute'), new Zend_Db_Expr("MAX(`sort_order`)"))
                ->where("$this->getTable('entity_attribute').attribute_group_id = ?", $object->getAttributeGroupId());
            $maxOrder = $select->fetchOne($select);
            return $maxOrder;
        }

        return 0;
    }
}