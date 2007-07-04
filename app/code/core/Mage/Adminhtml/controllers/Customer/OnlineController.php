<?php

class Mage_Adminhtml_Customer_OnlineController extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {
        $this->loadLayout('baseframe');
        $block = $this->getLayout()->createBlock('adminhtml/customer_online', 'customers');
        $this->getLayout()->getBlock('content')->append($block);

        $this->getLayout()->getBlock('breadcrumbs')
            ->addLink(__('customers'), __('online customers title'));

        $this->renderLayout();
    }
    
    /*public function onlineAction()
    {
        $this->loadLayout('baseframe');
        $this->getLayout()->getBlock('menu')->setActive('customer/online');
        $block = $this->getLayout()->createBlock('adminhtml/customer_online', 'customer_online');
        $this->getLayout()->getBlock('content')->append($block);

        $this->getLayout()->getBlock('breadcrumbs')
            ->addLink(__('customers'), __('customers title'));

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
