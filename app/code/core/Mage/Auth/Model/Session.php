<?php

class Mage_Auth_Model_Session extends Mage_Core_Model_Session_Abstract 
{
    public function __construct()
    {
        $this->init('auth');
    }
}