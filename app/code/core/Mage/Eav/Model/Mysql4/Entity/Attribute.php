<?php

class Mage_Eav_Model_Mysql4_Entity_Attribute extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('eav/attribute', 'attribute_id');
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

    public function itemExists($object)
    {
        $read = $this->getConnection('read');

        $select = $read->select()->from($this->getMainTable())
            ->where("attribute_code='{$object->getAttributeCode()}'")
            ->where('attribute_id != ?', $object->getAttributeId())
            ->where('entity_type_id = ?', $object->getEntityTypeId());

        $data = $read->fetchRow($select);

        if (!$data) {
            return false;
        }
        return true;
    }

    public function save(Mage_Core_Model_Abstract $object)
    {
        $write = $this->getConnection('write');
        $attributeId = $object->getId();

        $data = $object->getData();
        unset($data['attribute_set_id']);
        unset($data['attribute_group_id']);
        unset($data['sort_order']);
        unset($data['force_update']);

        $write->beginTransaction();
        try {
            if( !$object->getSortOrder() ) {
                if( $attributeId > 0 ) {
                    $condition = $write->quoteInto("{$this->getMainTable()}.{$this->getIdFieldName()} = ?", $attributeId);
                    $write->update($this->getMainTable(), $data, $condition);
                } else {
                    $write->insert($this->getMainTable(), $data);
                }
            }

            $data = array(
                'entity_type_id' => ( $object->getEntityTypeId() > 0 ) ? $object->getEntityTypeId() : 0,
                'attribute_set_id' => ( $object->getAttributeSetId() > 0 && $object->getAttributeGroupId() > 0 ) ? $object->getAttributeSetId() : 0,
                'attribute_group_id' => ( $object->getAttributeGroupId() > 0 ) ? $object->getAttributeGroupId() : 0,
                'attribute_id' => ( $object->getId() ) ? $object->getId() : $write->lastInsertId(),
                'sort_order' => ( ( $object->getSortOrder() ) ? $object->getSortOrder() : $this->_getMaxSortOrder($object) + 1 ),
            );

            $condition = "({$this->getTable('entity_attribute')}.attribute_set_id = '{$object->getAttributeSetId()}' AND {$this->getTable('entity_attribute')}.attribute_id = '{$object->getId()}')";
            $condition.= "OR ({$this->getTable('entity_attribute')}.attribute_group_id = '0' AND {$this->getTable('entity_attribute')}.attribute_id = '{$object->getId()}')";
            $write->delete($this->getTable('entity_attribute'), $condition);

            if( $this->_attributeInZeroSet($object) == false ) {
                $write->insert($this->getTable('entity_attribute'), $data);
            }

            $write->commit();
        } catch (Exception $e) {
            $write->rollback();
            throw new Exception($e->getMessage());
        }
        return $object;
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
        return $object;
    }
}