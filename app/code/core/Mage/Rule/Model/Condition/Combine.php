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
        $condition->setRule($this->getRule());
        $condition->setObject($this->getObject());

        $conditions = $this->getConditions();        
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
     *     {condition::asArray},
     *     {combine::asArray},
     *     {quote_item_combine::asArray}
     *   )
     * )
     * 
     * @return array
     */
    public function asArray(array $arrAttributes = array())
    {
        $out = parent::asArray();
        
        foreach ($this->getConditions() as $condition) {
            $out['conditions'][] = $condition->asArray();
        }
        
        return $out;
    }
    
    public function asXml()
    {
        extract($this->asArray());
        $xml = "<attribute>".$this->getAttribute()."</attribute>"
            ."<value>".$this->getValue()."</value>"
            ."<conditions>";
        foreach ($this->getConditions() as $condition) {
            $xml .= "<condition>".$condition->asXml()."</condition>";
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
            $this->addCondition($cond);            
            $cond->loadArray($condArr);
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
    
    public function asHtml()
    {
    	$form = $this->getRule()->getForm();
    	$renderer = new Mage_Rule_Block_Editable();
    	
    	$attrEl = $form->addField('cond:'.$this->getId().':attribute', 'select', array(
    		'values'=>$this->getAttributeSelectOptions(),
    		'value'=>$this->getAttribute(),
    		'value_name'=>$this->getAttributeName(),
    	))->setRenderer($renderer);
    	
    	$valueEl = $form->addField('cond:'.$this->getId().':value', 'select', array(
    		'values'=>$this->getValueSelectOptions(),
    		'value'=>$this->getValue(),
    		'value_name'=>$this->getValueName(),
    	))->setRenderer($renderer);
    	
       	$html = "If ".$attrEl->getHtml()." of these conditions are ".$valueEl->getHtml();
    	return $html;
    }
    
    public function asHtmlRecursive($level=0)
    {
        $html = parent::asHtmlRecursive($level);
        foreach ($this->getConditions() as $cond) {
            $html .= "<br>".$cond->asHtmlRecursive($level+1);
        }
        return $html;
    }
        
    public function asString($format='')
    {
        $str = "If ".$this->getAttributeName()." of these conditions are ".$this->getValueName();
        return $str;
    }
    
    public function asStringRecursive($level=0)
    {
        $str = parent::asStringRecursive($level);
        foreach ($this->getConditions() as $cond) {
            $str .= "\n".$cond->asStringRecursive($level+1);
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