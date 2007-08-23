<?php

class Mage_Core_Controller_Front_Action extends Mage_Core_Controller_Varien_Action 
{
	protected function _construct()
	{
		parent::_construct();
	
        #Mage::getConfig()->loadEventObservers('front');
	}
	
	public function _redirectToReferer()
	{
	    $referer = $this->getRequest()->getServer('HTTP_REFERER');
	    if (!$referer) {
	        $referer = Mage::getBaseUrl();
	    }
	    $this->getResponse()->setRedirect($referer);
	    return $this;
	}
}