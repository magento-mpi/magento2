<?php

class Mage_Rule_Model_Condition_Combine extends Mage_Rule_Model_Condition_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('combine')
            ->setAttribute('all')
            ->setValue(true)
            ->setConditions(array());
    }
    
    public function loadAttributeOptions()
    {
        $this->setAttributeOption(array(
            'all' => 'ALL',
            'any' => 'ANY',
        ));
        return $this;
    }
    
    public function addCondition(Mage_Rule_Model_Condition_Interface $condition)
    {
        $conditions = $this->getConditions();
        
        $condition->setRule($this->getRule());
        $condition->setObject($this->getObject());

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
    
    public function toXml()
    {
        extract($this->toArray());
        $xml = "<attribute>".$this->getAttribute()."</attribute>"
            ."<value>".$this->getValue()."</value>"
            ."<conditions>";
        foreach ($this->getConditions() as $condition) {
            $xml .= "<condition>".$condition->toXml()."</condition>";
        }
        $xml .= "</conditions>";
        return $xml;
    }
    
    public function loadArray($arr)
    {
        $this->setAttribute($arr['attribute'])
            ->setValue($arr['value']);
        
        foreach ($arr['conditions'] as $condArr) {
            $cond = $this->getRule()->getConditionInstance($condArr['type']);
            $cond->loadArray($condArr);
            $this->addCondition($cond);
        }
        return $this;
    }
    
    public function loadXml($xml)
    {
        if (is_string($xml)) {
            $xml = simplexml_load_string($xml);
        }
        $arr = parent::loadXml($xml);
        foreach ($xml->conditions->children() as $condition) {
            $arr['conditions'] = parent::loadXml($condition);
        }
        $this->loadArray($arr);
        return $this;
    }
    
    public function getValueName()
    {
        return $this->getValueOption((int)$this->getValue());
    }
        
    public function toString($format='')
    {
        $str = "If ".$this->getAttributeName()." of these conditions are ".$this->getValueName();
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
    
    public function validate()
    {
        $all = $this->getAttribute()==='all';
        $true = (bool)$this->getValue();
        foreach ($this->getConditions() as $cond) {
            if ($all && $cond->validate()!==$true) {
                return false;
            } elseif (!$all && $cond->validate()===$true) {
                return true;
            }
        }
        return $all ? true : false;
    }
}