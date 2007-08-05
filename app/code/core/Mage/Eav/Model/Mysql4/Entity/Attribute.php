<?php

class Mage_Eav_Model_Mysql4_Entity_Attribute extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('eav/attribute', 'attribute_id');
        $this->_uniqueFields = array( array('field' => array('attribute_code','entity_type_id'), 'title' => __('Attribute with the same code') ) );
    }

    public function loadByCode($object, $entityTypeId, $code)
    {
        $read = $this->getConnection('read');

        $select = $read->select()->from($this->getMainTable())
            ->where('entity_type_id=?', $entityTypeId)
            ->where('attribute_code=?', $code);
        $data = $read->fetchRow($select);

        if (!$data) {
            return false;
        }

        $object->setData($data);

        $this->_afterLoad($object);

        return true;
    }

    public function save(Mage_Core_Model_Abstract $object)
    {
        $write = $this->getConnection('write');
        $attributeId = $object->getId();

        $write->beginTransaction();
        try {

            $this->_beforeSave($object);

            $this->_checkUnique($object);

            if( !$object->getSortOrder() ) {
                $data = $object->getData();
        
                unset($data['attribute_set_id']);
                unset($data['attribute_group_id']);
                unset($data['sort_order']);
                unset($data['force_update']);
                
                if( $attributeId > 0 ) {
                    $condition = $write->quoteInto("{$this->getMainTable()}.{$this->getIdFieldName()} = ?", $attributeId);
                    $write->update($this->getMainTable(), $data, $condition);
                } else {
                    $write->insert($this->getMainTable(), $data);
                    $object->setId($write->lastInsertId());
                }
            }

            $this->_afterSave($object);
            $write->commit();
        }
        catch (Mage_Core_Exception $e) {
            $write->rollBack();
            Mage::throwException($e->getMessage());
        }
        catch (Exception $e) {
            $write->rollBack();
            Mage::throwException('Exception while saving the object:' . $e->getMessage());
        }

        return $this;
    }

    private function _getMaxSortOrder($object)
    {
        if( intval($object->getAttributeGroupId()) > 0 ) {
            $read = $this->getConnection('read');
            $select = $read->select()
                ->from($this->getTable('entity_attribute'), new Zend_Db_Expr("MAX(`sort_order`)"))
                ->where("{$this->getTable('entity_attribute')}.attribute_set_id = ?", $object->getAttributeSetId())
                ->where("{$this->getTable('entity_attribute')}.attribute_id = ?", $object->getId());
            $maxOrder = $read->fetchOne($select);
            return $maxOrder;
        }

        return 0;
    }

    protected function _attributeInZeroSet($object)
    {
        $read = $this->getConnection('read');

        $select = $read->select()->from($this->getTable('entity_attribute'))
            ->where("attribute_set_id=0")
            ->where('attribute_id = ?', $object->getId());

        $data = $read->fetchRow($select);

        if (!$data) {
            return false;
        }
        return true;
    }

    public function deleteEntity($object)
    {
        $write = $this->getConnection('write');

        $condition = $write->quoteInto("{$this->getTable('entity_attribute')}.entity_attribute_id = ?", $object->getEntityAttributeId());
        $write->delete($this->getTable('entity_attribute'), $condition);
        return $this;
    }
    
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $write = $this->getConnection('write');
        
        $table = $this->getTable('entity_attribute');
        
        $attrId = $object->getId();
        $setId = $object->getAttributeSetId();
        $groupId = $object->getAttributeGroupId();

        $data = array(
            'entity_type_id' => ( $object->getEntityTypeId() > 0 ) ? $object->getEntityTypeId() : 0,
            'attribute_set_id' => ( $setId > 0 && $groupId > 0 ) ? $setId : 0,
            'attribute_group_id' => ( $groupId > 0 ) ? $groupId : 0,
            'attribute_id' => $attrId,
            'sort_order' => ( ( $object->getSortOrder() ) ? $object->getSortOrder() : $this->_getMaxSortOrder($object) + 1 ),
        );

        $condition = "$table.attribute_id = '$attrId' AND ($table.attribute_set_id = '$setId' OR $table.attribute_group_id = '0')";
        $write->delete($table, $condition);

        if( $this->_attributeInZeroSet($object) == false ) {
            $write->insert($table, $data);
        }
        
        parent::_afterSave($object);
    }
}