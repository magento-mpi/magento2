<?php

class Mage_Core_Model_Email_Template_Filter extends Varien_Filter_Template 
{
	protected $_allowedDirectives = array('var', 'include', 'url');
	
	public function urlDirective($construction)
	{
    	$replacedValue = $this->_getVariable($construction[2], $construction[0]);
    	return $replacedValue;
	}
	
	protected function _getUrl($value)
	{
        $tokenizer = new Varien_Filter_Template_Tokenizer_Parameter();
        $tokenizer->setString($value);
        
        return $tokenizer->tokenize();
	}
}