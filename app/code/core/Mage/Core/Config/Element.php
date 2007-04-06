<?php

class Mage_Core_Config_Element extends Varien_Simplexml_Element
{
    function is($var, $value='true')
    {
        $flag = $this->$var;
        return !empty($flag) && ($value===strtolower((string)$value));
    }
}