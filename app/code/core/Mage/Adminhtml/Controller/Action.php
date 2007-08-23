<?php

class Mage_Adminhtml_Controller_Action extends Mage_Core_Controller_Varien_Action
{
    protected function _construct()
    {
        parent::_construct();

        Mage::getDesign()->setArea('adminhtml')
            ->setPackageName('default')
            ->setTheme('default');

        $this->getLayout()->setArea('adminhtml');   
    }
    
    protected function _setActiveMenu($menuPath)
    {
        $this->getLayout()->getBlock('menu')->setActive($menuPath);
        return $this;
    }

    protected function _addBreadcrumb($label, $title, $link=null)
    {
        $this->getLayout()->getBlock('breadcrumbs')->addLink($label, $title, $link);
        return $this;
    }

    protected function _addContent(Mage_Core_Block_Abstract $block)
    {
        $this->getLayout()->getBlock('content')->append($block);
        return $this;
    }

    protected function _addLeft(Mage_Core_Block_Abstract $block)
    {
        $this->getLayout()->getBlock('left')->append($block);
        return $this;
    }
    
    protected function _addJs(Mage_Core_Block_Abstract $block)
    {
        $this->getLayout()->getBlock('js')->append($block);
        return $this;
    }
    
    protected function _isAllowed()
    {
    	return true; #Mage::getSingleton('admin/session')->isAllowed('admin');
    }
    
    public function preDispatch()
    {
    	parent::preDispatch();

    	if ($this->getRequest()->isDispatched() 
    		&& $this->getRequest()->getActionName()!=='denied'
    		&& !$this->_isAllowed()) {
    		$this->_redirect('*/*/denied');
    		//$this->getRequest()->setDispatched(false);
    		$this->setFlag('', 'no-dispatch', true);
    	}
    	
    	return $this;
    }
    
    public function deniedAction()
    {
    	$this->loadLayout(array('baseframe', 'admin_denied'), 'admin_denied');
        $this->renderLayout();
    }
    
    public function loadLayout($ids=null, $key='', $generateBlocks=true)
    {
        parent::loadLayout($ids, $key, $generateBlocks);
        $this->_initLayoutMessages('adminhtml/session');
        return $this;
    }
    
    public function norouteAction($coreRoute = null)
    {
        $this->loadLayout(array('baseframe', 'admin_noroute'), 'admin_noroute');
        $this->renderLayout();
    }
}