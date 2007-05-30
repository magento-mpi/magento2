<?php

class Mage_Sales_Model_Quote_Rule_Condition_Combine extends Mage_Sales_Model_Quote_Rule_Condition_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('combine')
            ->setAttribute('all')
            ->setOperator('=')
            ->setValue(true)
            ->setConditions(array());
        $this->loadAttributes()->loadOperators()->loadValues();
    }
    
    public function loadAttributes()
    {
        $this->setAttributeOption(array(
            'all' => 'ALL',
            'any' => 'ANY',
        ));
        return $this;
    }
    
    public function loadOperators()
    {
        $this->setOperatorOption(array(
            '=' => 'are',
        ));
        return $this;
    }
    
    public function loadValues()
    {
        $this->setValueOption(array(
            1 => 'TRUE',
            0 => 'FALSE',
        ));
        return $this;
    }
    
    public function addCondition(Mage_Sales_Model_Quote_Rule_Condition_Abstract $condition)
    {
        $conditions = $this->getConditions();
        
        $condition->setRule($this->getRule());

        $conditions[] = $condition;
        if (!$condition->getId()) {
            $condition->setId($this->getId().'.'.sizeof($conditions));
        }
        
        $this->setConditions($conditions);
        return $this;
    }
    
    /**
     * Returns array containing conditions in the collection
     * 
     * Output example:
     * array(
     *   'type'=>'combine',
     *   'operator'=>'ALL',
     *   'value'=>'TRUE',
     *   'conditions'=>array(
     *     {condition::toArray},
     *     {combine::toArray},
     *     {quote_item_combine::toArray}
     *   )
     * )
     * 
     * @return array
     */
    public function toArray(array $arrAttributes = array())
    {
        $out = parent::toArray();
        
        foreach ($this->getConditions() as $condition) {
            $out['conditions'][] = $condition->toArray();
        }
        
        return $out;
    }
    
    public function loadArray($arr)
    {
        $salesConfig = Mage::getSingleton('sales', 'config');
        $this->setType('combine');
        
        foreach ($arr['conditions'] as $condArr) {
            $cond = $salesConfig->getQuoteRuleConditionInstance($condArr['type']);
            $cond->loadArray($condArr);
            $this->addCondition($cond);
        }
        return $this;
    }
    
    public function getValueName()
    {
        return $this->getValueOption((int)$this->getValue());
    }
        
    public function toString($format='')
    {
        $str = "If ".$this->getAttributeName()." of these conditions ".$this->getOperatorName()." ".$this->getValueName().":";
        return $str;
    }
    
    public function toStringRecursive($level=0)
    {
        $str = parent::toStringRecursive($level);
        foreach ($this->getConditions() as $cond) {
            $str .= "\n".$cond->toStringRecursive($level+1);
        }
        return $str;
    }
}