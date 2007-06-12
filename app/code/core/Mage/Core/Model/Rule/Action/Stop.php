<?php

class Mage_Core_Model_Rule_Action_Stop extends Mage_Core_Model_Rule_Action_Abstract
{
    public function toArray(array $arrAttributes = array())
    {
        return array('type'=>'stop');
    }
    
    public function toString($format='')
    {
        $str = "Stop rule processing";
        return $str;
    }
}