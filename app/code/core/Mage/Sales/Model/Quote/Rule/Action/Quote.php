<?php

class Mage_Sales_Model_Quote_Rule_Action_Quote extends Mage_Rule_Model_Action_Abstract
{
    public function loadAttributeOptions()
    {
        $this->setAttributeOption(array(
            'subtotal'=>'Subtotal',
            'shipping_amount'=>'Shipping amount',
            'shipping_method'=>'Shipping method',
            'discount_amount'=>'Discount amount',
            'discount_percent'=>'Discount percent',
            'weight'=>'Weight',
        ));
        return $this;
    }
    
    public function loadArray(array $arr)
    {
        $this->addData(array(
            'attribute'=>$arr['attribute'],
            'operator'=>$arr['operator'],
            'value'=>$arr['value'],
        ));
        return parent::loadArray($arr);
    }
    
    public function asArray(array $arrAttributes = array())
    {
        $arr = array(
            'type'=>'quote', 
            'attribute'=>$this->getAttribute(),
            'operator'=>$this->getOperator(),
            'value'=>$this->getValue(),
        );
        return $arr;
    }
    
    public function asHtml()
    {
    	$form = $this->getRule()->getForm();
    	$renderer = new Mage_Rule_Block_Editable();
    	
    	$typeEl = $form->addField('action:'.$this->getId().':type', 'hidden', array(
    		'name'=>'rule[actions]['.$this->getId().'][action]',
    		'value'=>$this->getType(),
    		'no_span'=>true,
    	));
    	    	
    	$attrEl = $form->addField('action:'.$this->getId().':attribute', 'select', array(
    		'name'=>'rule[actions]['.$this->getId().'][attribute]',
    		'values'=>$this->getAttributeSelectOptions(),
    		'value'=>$this->getAttribute(),
    		'value_name'=>$this->getAttributeName(),
    	))->setRenderer($renderer);
    	
    	$operEl = $form->addField('action:'.$this->getId().':operator', 'select', array(
    		'name'=>'rule[actions]['.$this->getId().'][operator]',
    		'values'=>$this->getOperatorSelectOptions(),
    		'value'=>$this->getOperator(),
    		'value_name'=>$this->getOperatorName(),
    	))->setRenderer($renderer);
    	
    	$valueEl = $form->addField('action:'.$this->getId().':value', 'text', array(
    		'name'=>'rule[actions]['.$this->getId().'][value]',
    		'value'=>$this->getValue(),
    		'value_name'=>$this->getValueName(),
    	))->setRenderer($renderer);
    	
        $str = $typeEl->getHtml()."Update cart ".$attrEl->getHtml().' '.$operEl->getHtml().' '.$valueEl->getHtml();
        return $str;
    }
    
    public function asString($format='')
    {
        $str = "Update cart ".$this->getAttributeName()
            ." ".$this->getOperatorName()." ".$this->getValueName();
        return $str;
    }
    
    public function process()
    {
        switch ($this->getOperator()) {
            case '=':
                $value = $this->getValue();
                break;
                
            case '+=':
                $value = $this->getObject()->getData($this->getAttribute())+$this->getValue();
        }
        $quote->setData($this->getAttribute(), $value);
        
        return $this;
    }
}