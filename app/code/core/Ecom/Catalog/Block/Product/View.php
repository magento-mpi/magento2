<?php

#include_once "Ecom/Core/Block/Template.php";

/**
 * Product View block
 *
 * @package    Ecom
 * @module     Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Ecom_Catalog_Block_Product_View extends Ecom_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setViewName('Ecom_Catalog', 'product/view');
    }

    public function loadData(Zend_Controller_Request_Http $request)
    {
        $productId = $request->getParam('id');
        $product = Ecom::getModel('catalog', 'product', array('id'=>$productId));
        $product->load($productId);
        
        $breadcrumbs = Ecom::createBlock('catalog_breadcrumbs', 'catalog.breadcrumbs');
        $breadcrumbs->addCrumb('home', array('label'=>'Home','title'=>'Go to home page','link'=>Ecom::getBaseUrl().'/'));
        $breadcrumbs->addCrumb('category', array('label'=>$product->getCategoryName(), 'title'=>'', 'link'=>$product->getCategoryLink()));
        $breadcrumbs->addCrumb('product', array('label'=>$product->getName()));
        $this->setChild('breadcrumbs', $breadcrumbs);
        
        $this->assign('product', $product);
    }
}