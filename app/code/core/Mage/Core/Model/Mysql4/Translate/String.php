<?php
/**
 * String translate resource model
 *
 * @package     Mage
 * @subpackage  Core
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Core_Model_Mysql4_Translate_String extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('core/translate', 'key_id');
    }
    
    public function load(Mage_Core_Model_Abstract $object, $value, $field=null)
    {
        if (is_string($value)) {
            $field = 'string';
        }
        return parent::load($object, $value, $field);
    }
    
    protected function _getLoadSelect($field, $value)
    {
        $select = parent::_getLoadSelect($field, $value);
        $select->where('store_id', 0);
        return $select;
    }

    
    public function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $connection = $this->getConnection('read');
        $select = $connection->select()
            ->from($this->getMainTable(), array('store_id', 'translate'))
            ->where('string=?', $object->getString());
        $translations = $connection->fetchPairs($select);
        $object->setStoreTranslations($translations);
        return parent::_afterLoad($object);
    }
    
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $connection = $this->getConnection('write');
        $select = $connection->select()
            ->from($this->getMainTable(), 'key_id')
            ->where('string=?', $object->getString())
            ->where('store_id=?', 0);
            
        $object->setId($connection->fetchOne($select));
        return parent::_beforeSave($object);
    }
    
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $connection = $this->getConnection('write');
        $select = $connection->select()
            ->from($this->getMainTable(), array('store_id', 'key_id'))
            ->where('string=?', $object->getString());
        $stors = $connection->fetchPairs($select);
        
        $translations = $object->getStoreTranslations();
        
        if (is_array($translations)) {
            foreach ($translations as $storeId => $translate) {
                $condition = $connection->quoteInto('store_id=? AND ', $storeId) .
                    $connection->quoteInto('string=?', $object->getString());
                    
            	if (empty($translate)) {
            	    $connection->delete($this->getMainTable(), $condition);
            	}
            	else {
            	    $data = array(
            	       'store_id'  => $storeId,
            	       'string'    => $object->getString(),
            	       'translate' =>$translate, 
                    );
                    
            	    if (isset($stors[$storeId])) {
            	        $connection->update(
            	           $this->getMainTable(), 
            	           $data,
            	           $connection->quoteInto('key_id=?', $stors[$storeId]));
            	    }
            	    else {
            	        $connection->insert($this->getMainTable(), $data);
            	    }
            	}
            }
        }
        return parent::_afterSave($object);
    }
}
