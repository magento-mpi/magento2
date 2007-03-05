<?php



class Ecom_Catalog_IndexController extends Ecom_Core_Controller_Action {

    /**
     * Index action
     *
     * Display categories home page
     *
     */
    function indexAction() 
    {
        $breadcrumbs = Ecom::createBlock('catalog_breadcrumbs', 'catalog.breadcrumbs');
        $breadcrumbs->addCrumb('home', array('label'=>'Home'));
        Ecom::getBlock('content')->append($breadcrumbs);
    }

    function testAction() 
    {
        echo __METHOD__;
    }
}

