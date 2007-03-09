<?php

class IndexController extends Mage_Core_Controller_Admin_Action
{
    function indexAction()
    {
        #$this->renderLayout();
    }
    
    function dashboardAction()
    {
        echo "Dashboard";
    }
}