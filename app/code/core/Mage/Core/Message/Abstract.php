<?php

abstract class Mage_Core_Message_Abstract
{
    protected $_data = null;
    
    function __construct($data)
    {
        $this->_data = $data;
    }
    
    function getType()
    {
        return 'message';
    }
}