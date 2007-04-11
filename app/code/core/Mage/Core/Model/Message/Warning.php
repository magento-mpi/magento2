<?php

class Mage_Core_Model_Message_Warning extends Mage_Core_Model_Message_Abstract
{
    public function __construct($code)
    {
        parent::__construct(Mage_Core_Model_Message::WARNING, $code);
    }
}