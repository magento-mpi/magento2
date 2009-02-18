<?php
class Enterprise_Logging_Model_Event extends Mage_Core_Model_Abstract {
    public function __construct() {
        $this->_init('logging/event');
    }

    public function setEvent($name) {
        $node = Mage::getConfig()->getNode('enterprise/logging/events');
        foreach($node->children() as $child) {
            if($name == strtolower($child->label)) {
                $this->setEventCode((string)$child->code);
                return;
            }
        }
        Mage::throwException('Unknown event "'.$name.'"');
    }
}