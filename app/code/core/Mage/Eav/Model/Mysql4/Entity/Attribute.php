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

    public function save(Mage_Core_Model_Abstract $object)
    {
        echo "debug: <pre>";
        print_r($object);
        echo "</pre>";
        die();
    }
}