<?php

class Mage_Paygate_Model_Mysql4_Authorizenet_Debug_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract 
{
	protected function _construct()
	{
		$this->_init('paygate/authorizenet_debug');
	}
}