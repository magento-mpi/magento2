<?php

class IndexController extends Mage_Core_Controller_Admin_Action
{
    function indexAction()
    {
        $this->loadLayout('admin', 'default'); 
        
        $head = Mage::getBlock('head');
        $children = $head->getChild();
        foreach ($children as $name=>$child) {
            if (preg_match('#^head\.js\.#', $name)) {
                $head->unsetChild($name);
            }
        }
               
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