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
    public function indexAction()
    {

    }

    public function viewAction()
    {
        $id = $this->getRequest()->getParam('id', false);
        $this->loadLayout();
        
        $product = Mage::getModel('catalog', 'product')->load($id);
        
        if ($product->getCustomLayout()) {
            $output = Mage::getModel('core', 'layout')->setXml($product->getCustomLayout())->getOutput();
            $customBlock = $this->getLayout()->createBlock('text')->setText($output);
            $this->getLayout()->getBlock('content')->append($customBlock);
        } else {
            $productInfoBlock = $this->getLayout()->createBlock('catalog_product_view', 'product.info');
            $productInfoBlock->loadData();
            $this->getLayout()->getBlock('content')->append($productInfoBlock);
        }
        
        $this->renderLayout();
    }

    public function imageAction()
    {
        $product = Mage::getModel('catalog_resource', 'product');
        $product->load($this->getRequest()->getParam('id'));
        $this->getLayout()->createBlock('tpl', 'root')->setTemplate('catalog/product/large.image.phtml')
            ->assign('product', $product);
        
    }
}