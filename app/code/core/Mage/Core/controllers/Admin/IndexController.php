<?php

class IndexController extends Mage_Core_Controller_Admin_Action
{
    function indexAction()
    {
        $this->loadLayout('admin', 'default'); 
        
        $head = $this->getLayout()->getBlock('head');
        $children = $head->getChild();
        
//        foreach ($children as $name=>$child) {
///*
// head.js.yui.utilities
// head.js.ext.yui.adapter
// head.js.ext.all.debug
// head.js.magenta.mage
// head.js.magenta.form
// head.js.magenta.core
// head.js.magenta.core.menu
// head.js.auth.menu
// head.js.customer.menu
// head.js.catalog
// head.js.catalog.product
// head.js.catalog.category
// head.js.catalog.menu
// head.js.sales.menu
// */            
//        if (preg_match('#^head\.js\.#', $name) 
//            && $name != 'head.js.yui.utilities'
//            && $name != 'head.js.ext.yui.adapter'
//            && $name != 'head.js.ext.all.debug'
//            && $name != 'head.js.magenta.mage'
//            && $name != 'head.js.magenta.form'
//            && $name != 'head.js.magenta.core'
//            && $name != 'head.js.magenta.core.menu'
//            && $name != 'head.js.auth.menu'
//
//) {
//                $head->unsetChild($name);
//            }
//        }
               
        $this->renderLayout();
    }
    
    function dashboardAction()
    {
        echo "Dashboard";
	}
	
	function applyDbUpdatesAction()
	{
	    Mage_Core_Resource_Setup::applyAllUpdates();
	    echo "Successfully updated.";
	}
}