<?php

abstract class Mage_Sales_Model_Quote_Rule_Action_Abstract extends Varien_Object
{
    public function __construct()
    {
        parent::__construct();
        $this->setActions(array())->setStopProcessingRules(false);
        $this->loadAttributes()->loadOperators()->loadValues();
    }
    
    public function toArray(array $arrAttributes = array())
    {
        return array();
    }
    
    public function loadArray($arr)
    {
        $this->setType($arr['type']);
        return $this;
    }
    
    public function loadAttributes()
    {
        return $this;
    }
    
    public function getAttributeName()
    {
        return $this->getAttributeOption($this->getAttribute());
    }
    
    public function loadOperators()
    {
        $this->setOperatorOption(array(
            'to' => 'to',
            'by' => 'by',
        ));
        return $this;
    }
    
    public function getOperatorName()
    {
        return $this->getOperatorOption($this->getOperator());
    }
    
    public function loadValues()
    {
        return $this;
    }
    
    public function getValueName()
    {
        return $this->getValue();
    }
    
    public function toString($format='')
    {
        return "";
    }
    
    public function toStringRecursive($level=0)
    {
        $str = str_pad('', $level*3, ' ', STR_PAD_LEFT).$this->toString();
        return $str;
    }
    
    public function updateQuote(Mage_Sales_Model_Quote $quote)
    {
        return $this;
    }
}