<?php

#include_once 'Ecom/Core/Controller/Zend/Action.php';

/**
 * Product controller
 *
 * @package    Ecom
 * @module     Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Ecom_Catalog_ProductController extends Ecom_Core_Controller_Zend_Action
{
    function __construct(Zend_Controller_Request_Abstract $request,Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        parent::__construct($request, $response, $invokeArgs);
        
        $this->setFlag('image', 'no-preDispatch', true);
    }
    
    function indexAction()
    {

    }

    public function viewAction()
    {
        $productInfoBlock = Ecom::createBlock('catalog_product_view', 'product.info');
        $productInfoBlock->loadData($this->getRequest());

        Ecom::getBlock('content')->append($productInfoBlock);
    }

    public function imageAction()
    {
        $product = Ecom::getModel('catalog', 'product');
        $product->load($this->getRequest()->getParam('id'));
        Ecom::createBlock('tpl', 'root')->setViewName('Ecom_Catalog', 'product/large.image')
            ->assign('product', $product);
        
    }
}