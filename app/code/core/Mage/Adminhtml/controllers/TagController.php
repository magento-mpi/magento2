<?php
class Mage_Adminhtml_TagController extends Mage_Adminhtml_Controller_Action {
    public function indexAction() {
    	/*
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('catalog');
        $this->_addBreadcrumb(__('Catalog'), __('catalog title'));

        $this->_addContent($this->getLayout()->createBlock('tag/adminhtml'));
        $this->renderLayout();
        */
        
        $this->loadLayout('baseframe');

        $this->getLayout()->getBlock('left')->append($this->getLayout()->createBlock('tag/adminhtml'));

        $block = $this->getLayout()->createBlock('core/template')->setTemplate('tag/adminhtml/tags.phtml');
        $this->_addContent($block);

        $this->_addBreadcrumb(__('Customers'), __('customers title'));

        $this->renderLayout();
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
