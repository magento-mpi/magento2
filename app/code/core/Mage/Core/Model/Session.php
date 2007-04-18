<?php

/**
 * Core session model
 * 
 * @todo extend from Mage_Core_Model_Session_Abstract
 *
 */
class Mage_Core_Model_Session extends Mage_Core_Model_Session_Abstract
{
    public function __construct($data=array())
    {
        $this->init('core');
    }
}