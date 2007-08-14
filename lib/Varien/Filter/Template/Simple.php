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
		$replaceFrom = array();
		$replaceTo = array();
		foreach ($this->getData() as $k=>$v) {
			if (is_string($v)) {
				$replaceFrom[] = $this->_startTag.$k.$this->_endTag;
				$replaceTo[] = $v;
			}
		}
		return str_replace($replaceFrom, $replaceTo, $value);
	}
}