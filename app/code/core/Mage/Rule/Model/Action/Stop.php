<?php

class Mage_Rule_Model_Action_Stop extends Mage_Rule_Model_Action_Abstract
{
    public function asArray(array $arrAttributes = array())
    {
        return array('type'=>'stop');
    }
    
    public function asString($format='')
    {
        $str = "Stop rule processing";
        return $str;
    }
    
    public function process()
    {
        $this->getRule()->setStopProcessingRules(true);
        return $this;
    }
}