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
        $block = $this->getLayout()->createBlock('adminhtml/customer_online', 'customers');
        $this->_addContent($block);

        $this->_addBreadcrumb(__('Customers'), __('Customers Title'), Mage::getUrl('adminhtml',array('controller'=>'customer')));
        $this->_addBreadcrumb(__('Online Customers'), __('Online Customers Title'));

        $this->renderLayout();
    }
    
    public function gridAction()
    {
        $block = $this->getLayout()->createBlock('adminhtml/customer_online_grid');
        $this->getResponse()->setBody($block->toHtml());
    }

    /*public function onlineAction()
    {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('customer/online');
        $block = $this->getLayout()->createBlock('adminhtml/customer_online', 'customer_online');
        $this->_addContent($block);

        $this->_addBreadcrumb(__('Customers'), __('Customers Title'));

        $this->renderLayout();


        $collection = Mage::getResourceSingleton('log/visitor_collection')
            ->useOnlineFilter()
            ->load();

        foreach ($collection->getItems() as $item) {
        	$item->addIpData($item)
                 ->addCustomerData($item)
        	     ->addQuoteData($item);
        }
    }*/
}
