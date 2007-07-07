<?php
class Mage_Adminhtml_TagController extends Mage_Adminhtml_Controller_Action {
    public function indexAction() {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('catalog');
        $this->_addBreadcrumb(__('catalog'), __('catalog title'));
            
        $this->_addContent($this->getLayout()->createBlock('tag/adminhtml'));
        $this->renderLayout();
    }
    
    public function framesetAction() {
        $frameset = $this->getLayout()->createBlock('core/template')->setTemplate('tag/adminhtml/frameset.phtml');
        $this->getResponse()->setBody($frameset->toHtml());
    }
    
    public function menuAction() {
        $treeBlock = $this->getLayout()->createBlock('core/template')->setTemplate('tag/adminhtml/menu.phtml');
        $this->getResponse()->setBody($treeBlock->toHtml());
    }
    
    public function mainAction() {
        $treeBlock = $this->getLayout()->createBlock('core/template')->setTemplate('tag/adminhtml/menu.phtml');
        $this->getResponse()->setBody($treeBlock->toHtml());
    }
    
    public function productsAction() {
    	$productsBlock = $this->getLayout()->createBlock('tag/adminhtml_products');
        $this->getResponse()->setBody($productsBlock->toHtml());
    }
    
    public function customersAction() {
    	$productsBlock = $this->getLayout()->createBlock('tag/adminhtml_customers');
        $this->getResponse()->setBody($productsBlock->toHtml());
    }
    
    public function pendingtagsAction() {
    	$productsBlock = $this->getLayout()->createBlock('tag/adminhtml_pending');
        $this->getResponse()->setBody($productsBlock->toHtml());
    }
    
    public function tagslistAction() {
    	$productsBlock = $this->getLayout()->createBlock('tag/adminhtml_tagslist');
        $this->getResponse()->setBody($productsBlock->toHtml());
    }
    
    public function approveAction() {
    	$tags = $this->getRequest()->getParam('tags', false);    	
    	
    	if ($tags) {    		
    		foreach ($tags as $tag => $val) {
    			Mage::getSingleton('tag/tag')
    				->setId($tag)
    				->setStatus($val)
    				->update();
    		}
    	}
    	
    	$this->pendingtagsAction();
    }
}
