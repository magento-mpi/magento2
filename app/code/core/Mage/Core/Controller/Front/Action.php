<?php

class Mage_Core_Controller_Front_Action extends Mage_Core_Controller_Varien_Action 
{
	protected function _construct()
	{
		parent::_construct();
	
        #Mage::getConfig()->loadEventObservers('front');
	}
}