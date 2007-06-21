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
    public function viewAction()
    {
        $id = $this->getRequest()->getParam('id', false);
        $this->loadLayout();
        
        $product = Mage::getModel('catalog/product')->load($id);
        if ($product->getCustomLayout()) {
            $this->getLayout()->loadString($product->getCustomLayout());
        } else {
            $this->getLayout()->loadUpdateFile(Mage::getWebsiteDir('layout').DS.'catalog'.DS.'defaultProduct.xml');
        }
        $this->getLayout()->generateBlocks();
        
        $this->renderLayout();
    }

    public function imageAction()
    {
        $product = Mage::getResourceModel('catalog/product');
        $product->load($this->getRequest()->getParam('id'));
        $this->getLayout()->createBlock('core/template', 'root')->setTemplate('catalog/product/large.image.phtml')
            ->assign('product', $product);
        
    }
}