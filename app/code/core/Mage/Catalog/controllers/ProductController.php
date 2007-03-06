<?php



/**
 * Product controller
 *
 * @package    Mage
 * @module     Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_ProductController extends Mage_Core_Controller_Action
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
        $productInfoBlock = Mage::createBlock('catalog_product_view', 'product.info');
        $productInfoBlock->loadData($this->getRequest());

        Mage::getBlock('content')->append($productInfoBlock);
    }

    public function imageAction()
    {
        $product = Mage::getModel('catalog', 'product');
        $product->load($this->getRequest()->getParam('id'));
        Mage::createBlock('tpl', 'root')->setViewName('Mage_Catalog', 'product/large.image')
            ->assign('product', $product);
        
    }
}