<?php

class Mage_Eav_Model_Mysql4_Entity_Store extends Mage_Core_Model_Mysql4_Abstract 
{
    protected function _construct()
    {
        $this->_init('eav/entity_store', 'entity_store_id');
    }
    
    /**
     * Load an object by entity type and store
     *
     * @param Varien_Object $object
     * @param integer $id
     * @param string $field field to load by (defaults to model id)
     * @return boolean
     */
    public function loadByEntityStore(Mage_Core_Model_Abstract $object, $entityTypeId, $storeId)
    {
        $read = $this->getConnection('read');

        $select = $read->select()->from($this->getMainTable())
            ->where('entity_type_id=?', $entityTypeId)
            ->where('store_id=?', $storeId);
        $data = $read->fetchRow($select);

        if (!$data) {
            return false;
        }

        $object->setData($data);

        $this->_afterLoad($object);

        return true;
    }
}