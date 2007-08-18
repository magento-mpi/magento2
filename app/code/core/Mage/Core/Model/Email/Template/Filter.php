<?php

class Mage_Core_Model_Email_Template_Filter extends Varien_Filter_Template 
{
	public function blockDirective($construction)
	{
		$blockParameters = $this->_getIncludeParameters($construction[2]);
		
		$type = $blockParameters['type'];
		
		$block = Mage::registry('action')->getLayout()->createBlock($type)
			->addData($blockParameters);
		
		return $block->toHtml();
	}
	
	protected function _getBlockParameters($value)
	{
        $tokenizer = new Varien_Filter_Template_Tokenizer_Parameter();
        $tokenizer->setString($value);
        
        return $tokenizer->tokenize();
	}
	
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