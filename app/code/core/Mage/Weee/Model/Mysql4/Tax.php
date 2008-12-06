<?php
class Mage_Weee_Model_Mysql4_Tax extends Mage_Core_Model_Mysql4_Abstract {
    protected function _construct()
    {
        $this->_init('weee/tax', 'value_id');
    }
    
    public function fetchOne($select)
    {
        return $this->_getReadAdapter()->fetchOne($select);
    }

    public function fetchCol($select)
    {
        return $this->_getReadAdapter()->fetchCol($select);
    }
}