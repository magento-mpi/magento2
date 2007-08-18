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

    public function deleteEntity($object)
    {
        $write = $this->getConnection('write');
        $condition = $write->quoteInto("{$this->getTable('entity_attribute')}.entity_attribute_id = ?", $object->getEntityAttributeId());
        $write->delete($this->getTable('entity_attribute'), $condition);
        return $this;
    }
    
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $frontendLabel = $object->getFrontendLabel();
        if (is_array($frontendLabel)) {
            if (empty($frontendLabel[0])) {
                Mage::throwException('Frontend label is not defined');
            }
            $object->setFrontendLabel($frontendLabel[0]);
            
            Mage::getModel('core/translate_string')
                ->setString($frontendLabel[0])
                ->setTranslate($frontendLabel[0])
                ->setStoreTranslations($frontendLabel)
                ->save();
        }
        
        return parent::_beforeSave($object);
    }
    
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $this->_saveInSetIncluding($object)
            ->_saveOption($object);
        return parent::_afterSave($object);
    }
    
    protected function _saveInSetIncluding(Mage_Core_Model_Abstract $object)
    {
        $attrId = $object->getId();
        $setId  = (int) $object->getAttributeSetId();
        $groupId= (int) $object->getAttributeGroupId();
        
        if ($setId && $groupId && $object->getEntityTypeId()) {
            $write = $this->getConnection('write');
            $table = $this->getTable('entity_attribute');
            
    
            $data = array(
                'entity_type_id' => $object->getEntityTypeId(),
                'attribute_set_id' => $setId,
                'attribute_group_id' => $groupId,
                'attribute_id' => $attrId,
                'sort_order' => (($object->getSortOrder()) ? $object->getSortOrder() : $this->_getMaxSortOrder($object) + 1),
            );
    
            $condition = "$table.attribute_id = '$attrId' 
                AND $table.attribute_set_id = '$setId'";
            $write->delete($table, $condition);
            $write->insert($table, $data);
        }
        return $this;
    }
    
    protected function _saveOption(Mage_Core_Model_Abstract $object)
    {
        $option = $object->getOption();
        if (is_array($option)) {
            $write = $this->getConnection('write');
            $optionTable        = $this->getTable('attribute_option');
            $optionValueTable   = $this->getTable('attribute_option_value');
            $stores = Mage::getModel('core/store')
                ->getResourceCollection()
                ->setLoadDefault(true)
                ->load();
            
            if (isset($option['value'])) {
                foreach ($option['value'] as $optionId => $values) {
                    $intOptionId = (int) $optionId;
                    if (!empty($option['delete'][$optionId])) {
                        if ($intOptionId) {
                            $condition = $write->quoteInto('option_id=?', $intOptionId);
                            $write->delete($optionTable, $condition);
                        }
                        
                        continue;
                    }
                    
                	if (!$intOptionId) {
                	    $data = array(
                	       'attribute_id'  => $object->getId(),
                	       'sort_order'    => isset($option['order'][$optionId]) ? $option['order'][$optionId] : 0,
                	    );
                	    $write->insert($optionTable, $data);
                	    $intOptionId = $write->lastInsertId();
                	}
                    
                	// Default value
                	if (!isset($values[0])) {
                	    Mage::throwException('Default otion value is not defined');
                	}
                	
                	$defaultValue = $values[0];
                	$write->delete($optionValueTable, $write->quoteInto('option_id=?', $intOptionId));
                	foreach ($stores as $store) {
                	    if (!empty($values[$store->getId()])) {
                    		$data = array(
                                'option_id' => $intOptionId,
                                'store_id'  => $store->getId(),
                                'value'     => $values[$store->getId()],
                    		);
                    		$write->insert($optionValueTable, $data);
                	    }
                	}
                }
            }
        }
        return $this;
    }
}