<?php

class Mage_Core_Model_Config_Element extends Varien_Simplexml_Element
{
    public function is($var, $value='true')
    {
        $flag = $this->$var;
        return !empty($flag) && (0===strcasecmp($value, (string)$flag));
    }
    
    public function getClassName()
    {
        return Mage::getConfig()->getModelClassName((string)$this->class);
    }
    
}