<?php

class Mage_Core_Model_Mysql4_Config_Data extends Mage_Core_Model_Mysql4_Abstract
{
    protected $_configFieldTable;
    
    public function __construct()
    {
        parent::__construct();
        $this->_configFieldTable = Mage::getSingleton('core/resource')->getTableName('core/config_field');
    }
    
    protected function _construct()
    {
        $this->_init('core/config_data', 'config_id');
    }
    
    /**
     * Perform actions after object save
     *
     * @param Varien_Object $object
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if ($backend = $this->_getPathBackend($object->getPath())) {
            $backend->afterSave($object);
        }
        return $this;
    }
    
    protected function _getPathBackend($path)
    {
        $read = $this->getConnection('read');
        $select = $read->select()
            ->from($this->_configFieldTable, 'backend_model')
            ->where($read->quoteInto('path=?', $path));
            
        $modelName = $read->fetchOne($select);
        if ($modelName) {
            return Mage::getSingleton($modelName);
        }
        return false;
    }
}