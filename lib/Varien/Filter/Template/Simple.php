<?php

class Varien_Filter_Template_Simple extends Varien_Object implements Zend_Filter_Interface
{
	protected $_startTag = '{{';
	protected $_endTag = '}}';
	
	public function setTags($start, $end)
	{
		$this->_startTag = $start;
		$this->_endTag = $end;
		return $this;
	}
	
	public function filter($value)
	{
		return preg_replace('#'.$this->_startTag.'(.*?)'.$this->_endTag.'#e', '$this->getData("$1")', $value);
	}
}