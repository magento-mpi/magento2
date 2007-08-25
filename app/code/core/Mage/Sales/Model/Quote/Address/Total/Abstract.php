<?php

abstract class Mage_Sales_Model_Quote_Address_Total_Abstract
{
	protected $_code;
	
	public function setCode($code) 
	{
		$this->_code = $code;
		return $this;
	}
	
	public function getCode()
	{
		return $this->_code;
	}
	
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        return $this;
    }
    
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $arr = array();
        
        return $arr;
    }
}