<?php

class Mage_Core_Config_Element extends Varien_Simplexml_Element
{
    public function is($var, $value='true')
    {
        $flag = $this->$var;
        return !empty($flag) && (0===strcasecmp($value, (string)$flag));
    }
    
    public function getClassName()
    {
        if ($this->model) {
            $className = Mage::getConfig()->getModelClassName((string)$this->model, $this->class);
        } else {
            $className = (string)$this->class;
        }
        return $className;
    }
    
}