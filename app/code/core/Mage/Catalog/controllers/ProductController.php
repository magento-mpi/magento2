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
            $this->getLayout()->loadUpdateFile(Mage::getSingleton('core/store')->getDir('layout').DS.'catalog'.DS.'defaultProduct.xml');
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
    
    public function addTagAction() {
	    	try {
		    	$tag_name = $this->getRequest()->getParam('tag_name', false);
		    	$pid = $this->getRequest()->getParam('prod_id', false);
		    	$uid = Mage::getSingleton('customer/session')->getCustomerId();
		    	
		    	$tags = Mage::getModel("catalog/tags");
		    	$tags->addTag($tag_name, $uid, $pid);   	
		    	$result = array('error' => 0,
	    					'error_message' => "test");
	    	} catch (Exception $e) {
	    		$result = array('error' => 1,
    					'error_message' => "You must register first");
	    	}    	
    	
    	$this->getResponse()->setBody(Zend_Json::encode($result));
    }
    
    public function updateTagAction() {    	
    	$id = $this->getRequest()->getParam('tag_id', false);
    	$new_name = $this->getRequest()->getParam('tag_name', false);
    	
    	$tags = Mage::getModel("catalog/tags");
    	$tags->updateTag($id, $new_name);   	
    	
    	$result = array('error' => 0,
    					'error_message' => "test");
    	
    	$this->getResponse()->setBody(Zend_Json::encode($result));
    }
    
    public function deleteTagAction() {
    	$id = $this->getRequest()->getParam('tag_id', false);
    	$uid = Mage::getSingleton('customer/session')->getCustomerId();
    	
    	$tags = Mage::getModel("catalog/tags");
    	$tags->deleteTag($id, $uid);   	
    	
    	$result = array('error' => 0,
    					'error_message' => "test");
    	
    	$this->getResponse()->setBody(Zend_Json::encode($result));
    }
}