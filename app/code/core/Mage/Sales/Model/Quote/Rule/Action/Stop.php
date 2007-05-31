<?php

class Mage_Sales_Model_Quote_Rule_Action_Stop extends Mage_Sales_Model_Quote_Rule_Action_Abstract
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
    
    public function updateQuote(Mage_Sales_Model_Quote $quote)
    {
        $this->getRule()->setStopProcessingRules(true);
        return $this;
    }
}