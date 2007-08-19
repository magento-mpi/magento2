<?php

class Mage_SalesRule_Model_Rule_Condition_Product_Combine extends Mage_Rule_Model_Condition_Combine 
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('salesrule/rule_condition_product_combine');
    }
    
    public function loadOperatorOptions()
    {
    	$this->setOperatorOption(array(
    		1=>'FOUND',
    		0=>'NOT FOUND',
    	));
    	return $this;
    }
    
    public function getNewChildSelectOptions()
    {
        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array('value'=>'salesrule/rule_condition_product', 'label'=>'Product attribute'),
        ));
        return $conditions;
    }
    
    public function asHtml()
    {
    	$html = $this->getTypeElement()->getHtml().
    		__("If an item is %s in the cart with %s of these conditions true:", 
    		$this->getOperatorElement()->getHtml(), $this->getAttributeElement()->getHtml());
       	$html.= ' ('.$this->getNewChildElement()->getHtml().')';
       	if ($this->getId()!='1') {
       	    $html.= $this->getRemoveLinkHtml();
       	}
    	return $html;
    }
}