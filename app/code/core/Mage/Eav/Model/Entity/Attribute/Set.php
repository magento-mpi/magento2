<?php

class Mage_Eav_Model_Entity_Attribute_Set extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('eav/entity_attribute_set');
    }

    public function organizeData($data)
    {
        $modelGroupArray = array();
        $modelAttributeArray = array();
        if( $data['groups'] ) {
            foreach( $data['groups'] as $group ) {
                $modelGroup = Mage::getModel('eav/entity_attribute_group');
                $modelGroup->setId($group[0])
                           ->setAttributeGroupName($group[1])
                           ->setAttributeSetId($this->getId())
                           ->setSortOrder($group[2]);

                if( $data['attributes'] ) {
                    foreach( $data['attributes'] as $key => $attribute ) {
                        if( $attribute[1] == $group[0] ) {
                            $modelAttribute = Mage::getModel('eav/entity_attribute');
                            $modelAttribute->setId($attribute[0])
                                           ->setAttributeGroupId($attribute[1])
                                           ->setAttributeSetId($this->getId())
                                           ->setEntityTypeId(Mage::registry('entityType'))
                                           ->setSortOrder($attribute[2]);
                            $modelAttributeArray[] = $modelAttribute;
                        }
                    }
                    $modelGroup->setAttributes($modelAttributeArray);
                    $modelAttributeArray = array();
                }
                $modelGroupArray[] = $modelGroup;
            }
            $this->setGroups($modelGroupArray);
        }


        if( $data['not_attributes'] ) {
            $modelAttributeArray = array();
            foreach( $data['not_attributes'] as $key => $attributeId ) {
                $modelAttribute = Mage::getModel('eav/entity_attribute');

                $modelAttribute->setEntityAttributeId($attributeId);
                $modelAttributeArray[] = $modelAttribute;
            }
            $this->setRemoveAttributes($modelAttributeArray);
        }

        if( $data['removeGroups'] ) {
            $modelGroupArray = array();
            foreach( $data['removeGroups'] as $key => $groupId ) {
                $modelGroup = Mage::getModel('eav/entity_attribute_group');
                $modelGroup->setId($groupId);

                $modelGroupArray[] = $modelGroup;
            }
            $this->setRemoveGroups($modelGroupArray);
        }

        $this->setAttributeSetName($data['attribute_set_name'])
            ->setEntityTypeId(Mage::registry('entityType'));
    }
}