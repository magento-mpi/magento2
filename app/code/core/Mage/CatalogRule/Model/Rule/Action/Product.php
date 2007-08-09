<?php

class Mage_CatalogRule_Model_Rule_Action_Product extends Mage_Rule_Model_Action_Abstract 
{
    public function loadAttributeOptions()
    {
        $this->setAttributeOption(array(
            'rule_price'=>__('Special price'),
        ));
        return $this;
    }
    
    public function loadOperatorOptions()
    {
        $this->setOperatorOption(array(
            'to_fixed'=>__('To Fixed Value'),
            'to_percent'=>__('To Percentage'),
            'by_fixed'=>__('By Fixed value'),
            'by_percent'=>__('By Percentage'),
        ));
        return $this;
    }
    
    public function asHtml()
    {
    	$form = $this->getRule()->getForm();
    	
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
    	))->setRenderer(Mage::getHelper('rule/editable'));
    	
    	$operEl = $form->addField('action:'.$this->getId().':operator', 'select', array(
    		'name'=>'rule[actions]['.$this->getId().'][operator]',
    		'values'=>$this->getOperatorSelectOptions(),
    		'value'=>$this->getOperator(),
    		'value_name'=>$this->getOperatorName(),
    	))->setRenderer(Mage::getHelper('rule/editable'));
    	
    	$valueEl = $form->addField('action:'.$this->getId().':value', 'text', array(
    		'name'=>'rule[actions]['.$this->getId().'][value]',
    		'value'=>$this->getValue(),
    		'value_name'=>$this->getValueName(),
    	))->setRenderer(Mage::getHelper('rule/editable'));
    	
        $html = $typeEl->getHtml().__("Update product's %s %s: %s", $attrEl->getHtml(), $operEl->getHtml(), $valueEl->getHtml());
        $html.= $this->getRemoveLinkHtml();
        return $html;
    }
}