<?php

class Mage_Core_Model_Message_Error extends Mage_Core_Model_Message_Abstract
{
    public function __construct($code)
    {
        parent::__construct(Mage_Core_Model_Message::ERROR, $code);
    }
}
