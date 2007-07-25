<?php

class Mage_Sales_Model_Quote_Rule_Condition_Quote_Item_Combine extends Mage_Rule_Model_Condition_Combine
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('quote_item_combine');
    }
    
    public function loadValueOptions()
    {
    	$this->setValueOption(array(
    		1=>'FOUND',
    		0=>'NOT FOUND',
    	));
    }
    
    public function setRule(Mage_Sales_Model_Quote_Rule $rule)
    {
        $this->setData('rule', $rule);
        $number = $rule->getFoundQuoteItemNumber();
        $rule->setFoundQuoteItemNumber($number+1);
        $this->setItemNumber($number);
        return $this;
    }
    
    public function addCondition(Mage_Rule_Model_Condition_Interface $condition)
    {
        $condition->setType('quote_item');
        parent::addCondition($condition);

        return $this;
    }
    
    public function asHtml()
    {
    	$form = $this->getRule()->getForm();
    	$renderer = new Mage_Rule_Block_Editable();
    	
    	$typeEl = $form->addField('cond:'.$this->getId().':type', 'hidden', array(
    		'name'=>'rule[conditions]['.$this->getId().'][type]',
    		'value'=>$this->getType(),
    		'no_span'=>true,
    	));
    	    	
    	$attrEl = $form->addField('cond:'.$this->getId().':attribute', 'select', array(
    		'name'=>'rule[conditions]['.$this->getId().'][attribute]',
    		'values'=>$this->getAttributeSelectOptions(),
    		'value'=>$this->getAttribute(),
    		'value_name'=>$this->getAttributeName(),
    	))->setRenderer($renderer);
    	
    	$valueEl = $form->addField('cond:'.$this->getId().':value', 'select', array(
    		'name'=>'rule[conditions]['.$this->getId().'][value]',
    		'values'=>$this->getValueSelectOptions(),
    		'value'=>$this->getValue(),
    		'value_name'=>$this->getValueName(),
    	))->setRenderer($renderer);
    	
       	$html = $typeEl->getHtml()."If an item is ".$valueEl->getHtml()
            .' in the cart with '.$attrEl->getHtml()
            ." of these conditions true (<strong># ".$this->getItemNumber()."</strong>)";
            
    	return $html;
    }
    
    public function asString($format='')
    {
        $str = "If an item is ".($this->getValue() ? 'FOUND' : 'NOT FOUND')
            .' in the cart with '.$this->getAttributeName()." of these conditions true (# ".$this->getItemNumber().")";
        return $str;
    }
    
    public function validate()
    {
        $all = $this->getAttribute()==='all';
        $found = false;
        foreach ($quote->getEntitiesByType('item') as $item) {
            $found = $all ? true : false;
            foreach ($this->getConditions() as $cond) {
                $cond->setObject($item);
                if ($all && !$cond->validate()) {
                    $found = false;
                    break;
                } elseif (!$all && $cond->validate()) {
                    $found = true;
                    break 2;
                }
            }
        }
        if ($found && $this->getValue()) { 
            // found an item and we're looking for existing one
            
            $foundItems = $this->getRule()->getFoundQuoteItems();
            $foundItems[$this->getItemNumber()] = $item->getEntityId();
            $this->getRule()->setFoundQuoteItems($foundItems);
            
            return true;
        } elseif (!$found && !$this->getValue()) {
            // not found and we're making sure it doesn't exist
            return true;
        }
        return false;
    }
}