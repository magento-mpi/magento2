<?php
class Enterprise_Logging_Model_Mysql4_Event_Configuration extends Mage_Core_Model_Mysql4_Abstract {
    protected function _construct() {
        $this->_init('logging/configuration', 'event_code');
    }

    public function getAllEvents() {
        $select = $this->_getReadAdapter()->select()
          ->from($this->getMainTable());
        $data = $this->_getReadAdapter()->fetchAll($select);
        
        $desc = array();
        $node = Mage::getConfig()->getNode('enterprise/logging/events');
        foreach($node->children() as $child) {
            foreach($data as &$event) {
                if($event['event_code'] == $child->code)
                    $event['label'] = (string)$child->label;
            }
        }
        return $data;
    }

    public function updateEvents($events) {
        $this->_getWriteAdapter()->query("UPDATE ".$this->getMainTable()." SET is_active=0"); // Unset all checkboxes 
        if(is_array($events)) { 
            $codes = array();
            // Check that codes is numeric
            foreach ($events as $code => $on) {
                if(is_numeric($code))
                    $codes[] = $code;
            }
            // Set selected checkboxes 
            $this->_getWriteAdapter()->query("UPDATE ".$this->getMainTable()." SET is_active=1 WHERE event_code IN(".implode(', ', $codes).")"); 
        }
    }
}