<?php

class IndexController extends Mage_Core_Controller_Admin_Action
{
    function indexAction()
    {
        $layout = $this->getLayout();
        
        $layout->init('admin_default');
        if (!$layout->isCacheLoaded()) {
            $layout->loadUpdatesFromConfig('admin', 'default');
            #$layout->saveCache();
        }
        
        $layout->createBlocks();
        
        $this->renderLayout();
    }
    
    function dashboardAction()
    {
        echo "Dashboard";
	}
}