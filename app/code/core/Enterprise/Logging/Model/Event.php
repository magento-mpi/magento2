<?php
class Enterprise_Logging_Model_Event extends Mage_Core_Model_Abstract {
    public function __construct() {
        $this->_init('logging/event');
    }
}