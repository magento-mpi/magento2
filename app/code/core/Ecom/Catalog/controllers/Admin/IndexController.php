<?php

#include_once "Ecom/Core/Controller/Zend/Admin/Action.php";
#include_once 'Varien/Widget/HTMLTree.php';

class Ecom_Catalog_IndexController extends Ecom_Core_Controller_Zend_Admin_Action 
{

    /**
     * Index action
     *
     * Display categories home page
     *
     */
    function indexAction() 
    {

    }

    function initBlockAction() 
    {
        $categories = Ecom::getModel('catalog','categories');
        $data = $categories->getTree();
        $tree = new Varien_Widget_HTMLTree($data);
        $tree->setHtmlId('catalog_tree');
        //$this->getResponse()->appendBody('<script src="'.Ecom::getBaseUrl('skin').'/catalog/js/tree.js"></script>');
        $this->getResponse()->appendBody($tree->render());
    }

    function __call($method, $args) 
    {
        var_dump($method);
    }
}
