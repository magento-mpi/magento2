<?php

class Mage_Adminhtml_Customer_OnlineController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
    	if($this->getRequest()->getParam('ajax')) {
    		$this->_forward('grid');
    		return;
    	}

        $this->loadLayout('baseframe');
        $this->_addContent($this->getLayout()->createBlock('adminhtml/customer_online', 'customers'));

        $this->_addBreadcrumb(__('Customers'), __('Customers'));
        $this->_addBreadcrumb(__('Online Customers'), __('Online Customers'));

        $this->renderLayout();
    }

    protected function _isAllowed()
    {
	    //print $this->getRequest()->getActionName();
    	return Mage::getSingleton('admin/session')->isAllowed('customer/online');
    }

}