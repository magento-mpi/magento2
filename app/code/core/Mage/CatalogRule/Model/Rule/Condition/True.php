<?php

class Mage_CatalogRule_Model_Rule_Condition_True extends Mage_Rule_Model_Condition_Abstract
{
	public function asHtml()
	{
		return $this->getTypeElement()->getHtml()
			.__('Match any product')
    	   .$this->getRemoveLinkHtml();
	}
	
	public function validate(Varien_Object $object) 
	{
		return true;
	}
	        
    public function collectValidatedAttributes($productCollection)
    {
        return $this;
    }
}