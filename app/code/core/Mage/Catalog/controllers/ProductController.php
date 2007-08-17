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
	protected function _initProductLayout()
    {
    	$this->loadLayout(null, '', false);
        $categoryId = $this->getRequest()->getParam('category', false);
        $productId  = $this->getRequest()->getParam('id');

        $product = Mage::getModel('catalog/product')
            ->load($productId)
            ->setCategoryId($categoryId);

        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            $this->_forward('noRoute');
            return;
        }

        Mage::register('product', $product);

        if ($product->getCustomLayout()) {
            $this->getLayout()->loadString($product->getCustomLayout());
        } else {
            $this->getLayout()->loadUpdateFile(Mage::getDesign()->getLayoutFilename('catalog/defaultProduct.xml'));
        }
        $this->getLayout()->generateBlocks();
    }
	
	public function viewAction()
    {
        $this->_initProductLayout();

        $this->renderLayout();
    }

    public function imageAction()
    {
        $product = Mage::getResourceModel('catalog/product');
        $product->load($this->getRequest()->getParam('id'));
        $this->getLayout()->createBlock('core/template', 'root')->setTemplate('catalog/product/large.image.phtml')
            ->assign('product', $product);
    }
        
    public function superConfigAction()
    {
    	$this->_initProductLayout();
        $this->getResponse()->setBody($this->getLayout()->getBlock('product.super.config')->toHtml());
    }
    
    public function priceAction()
    {
    	$this->_initProductLayout();
        $this->getResponse()->setBody($this->getLayout()->getBlock('product.info.price')->toHtml());
    }
}