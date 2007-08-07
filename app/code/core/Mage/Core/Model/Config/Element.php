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
        if ($this->class) {
            $model = (string)$this->class;
        } elseif ($this->model) {
            $model = (string)$this->model;
        } else {
            return false;
        }
        return Mage::getConfig()->getModelClassName($model);
    }
    
}