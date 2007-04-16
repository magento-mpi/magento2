<?php
/**
 * Product controller
 *
 * @package    Mage
 * @module     Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_ProductController extends Mage_Core_Controller_Front_Action
{
    protected function _construct()
    {
        parent::_construct();
        
        $this->setFlag('image', 'no-preDispatch', true);
    }
    
    public function indexAction()
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
        $product = Mage::getModel('catalog_resource', 'product');
        $product->load($this->getRequest()->getParam('id'));
        Mage::createBlock('tpl', 'root')->setViewName('Mage_Catalog', 'product/large.image.phtml')
            ->assign('product', $product);
        
    }
}