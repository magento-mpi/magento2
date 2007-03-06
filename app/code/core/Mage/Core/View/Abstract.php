<?php

abstract class Mage_Core_View_Abstract
{
    protected $_view = null;
    
    function __call($method, $args)
    {
        call_user_func_array(array($this->_view, $method), $args);
    }
}