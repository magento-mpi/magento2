<?php
class Enterprise_Logging_Model_Mysql4_Event_Collection extends  Mage_Core_Model_Mysql4_Collection_Abstract {

    protected function _construct() {
        $this->_init('logging/event');
    }

    public function load($printQuery = false, $logQuery = false) {
        $this->getSelect()->
          joinLeft('admin_user', 'main_table.user_id=admin_user.user_id');
        parent::load($printQuery, $logQuery);
        foreach($this->_items as &$item) {
            $node = Mage::getConfig()->getNode('enterprise/logging/events');
            $code = $item->getEventCode();
            foreach($node->children() as $child) {
                if($code == $child->getName()) {
                    $item->setEventLabel((string)$child->label);
                }
            }
        }
    }
}
?>