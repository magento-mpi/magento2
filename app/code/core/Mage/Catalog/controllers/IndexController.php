<?php



class Mage_Catalog_IndexController extends Mage_Core_Controller_Front_Action {

    /**
     * Index action
     *
     * Display categories home page
     *
     */
    function indexAction() 
    {
        $this->loadLayout();
            
        $breadcrumbs = Mage::createBlock('catalog_breadcrumbs', 'catalog.breadcrumbs');
        $breadcrumbs->addCrumb('home', array('label'=>'Home'));
        Mage::getBlock('content')->append($breadcrumbs);
        
        $this->renderLayout();
    }

    function testAction() 
    {
        echo __METHOD__;
    }
}

