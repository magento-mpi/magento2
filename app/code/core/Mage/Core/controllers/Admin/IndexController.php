<?php

class IndexController extends Mage_Core_Controller_Admin_Action
{
    function indexAction()
    {
        $this->loadLayout('admin', 'default');        
        $this->renderLayout();
    }
    
    function dashboardAction()
    {
        echo "Dashboard";
	}
	
	function applyDbUpdatesAction()
	{
	    Mage::getConfig()->applyDbUpdates();
	    echo "Successfully updated.";
	}
}