<?php

class Mage_Adminhtml_OnlineController extends Mage_Core_Controller_Front_Action
{

    public function IndexAction()
    {
        $this->loadLayout('baseframe');
        $block = $this->getLayout()->createBlock('adminhtml/customers', 'customers');
        $this->getLayout()->getBlock('content')->append($block);

        $this->getLayout()->getBlock('breadcrumbs')
            ->addLink(__('customers'), __('customers title'));

        $this->renderLayout();


        $collection = Mage::getSingleton('log_resource/customer_collection')
            ->useOnlineFilter()
            ->load();

        foreach ($collection->getItems() as $item) {
        	$item->addIpData($item)
                 ->addCustomerData($item)
        	     ->addQuoteData($item);
        }
    }
}
