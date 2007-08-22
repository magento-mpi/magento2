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
	protected function _initProduct()
    {
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
    }
	
	public function viewAction()
    {
        $this->_initProduct();
        
        $this->loadLayout(null, '', false);
        $product = Mage::registry('product');
        if ($product->getCustomLayout()) {
            $this->getLayout()->loadString($product->getCustomLayout());
        } else {
            $this->getLayout()->loadUpdateFile(Mage::getDesign()->getLayoutFilename('catalog/defaultProduct.xml'));
        }
        $this->getLayout()->generateBlocks();

        $head = $this->getLayout()->getBlock('head');
        $title = $head->getTitle();
        if ($category = $product->getCategory()) {
        	$title = ($category->getMetaTitle() ? $category->getMetaTitle() : $category->getName()).' - '.$title;
        }
        $title = ($product->getMetaTitle() ? $product->getMetaTitle() : $product->getName()).' - '.$title;
        $this->_initLayoutMessages('catalog/session');
        $head->setTitle($title);
        $this->renderLayout();
    }

    public function superConfigAction()
    {
    	$this->_initProduct();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('catalog/product_view_super_config')->toHtml()
        );
    }
    
    public function priceAction()
    {
    	$this->_initProduct();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('catalog/product_view_price')->toHtml()
        );
    }
    
    public function galleryAction()
    {
        $this->_initProduct();
        $this->loadLayout(array('default', 'catalog_product_gallery'), 'catalog_product_gallery');
        $this->renderLayout();
    }
}