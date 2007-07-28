<?php

class Mage_Eav_Model_Mysql4_Entity_Attribute_Set extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('eav/attribute_set', 'attribute_set_id');
    }

    public function save(Mage_Core_Model_Abstract $object)
    {
        $write = $this->getConnection('write');
        $setId = $object->getId();

        $data = array(
            'entity_type_id' => $object->getEntityTypeId(),
            'attribute_set_name' => $object->getAttributeSetName(),
        );

        $write->beginTransaction();
        try {
            if( intval($setId) > 0 ) {
                $condition = $write->quoteInto("{$this->getMainTable()}.{$this->getIdFieldName()} = ?", $setId);
                $write->update($this->getMainTable(), $data, $condition);

                if( $object->getGroups() ) {
                    foreach( $object->getGroups() as $group ) {
                        $group->save();
                    }
                }

                if( $object->getRemoveGroups() ) {
                    foreach( $object->getRemoveGroups() as $group ) {
                        $group->delete($group->getId());
                    }
                }

                if( $object->getRemoveAttributes() ) {
                    foreach( $object->getRemoveAttributes() as $attribute ) {
                        $attribute->setAttributeGroupId(0)
                                  ->setForceUpdate(true)
                                  ->save();
                    }
                }

            } else {
                $this->write->insert($this->getMainTable(), $data);
            }
            $write->commit();
        } catch (Exception $e) {
            $write->rollback();
            throw new Exception($e);
        }
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {

    }
}