<?php

class Mage_Adminhtml_OnlineController extends Mage_Core_Controller_Front_Action
{

    public function IndexAction()
    {
        $collection = Mage::getSingleton('log_resource/customer_collection')
            ->useOnlineFilter()
            ->load();

        foreach ($collection->getItems() as $item) {
        	$item->addIpData($item)
                 ->addCustomerData($item)
        	     ->addQuoteData($item);
        }

        echo "<pre>";
        print_r($collection->getItems());
        echo "</pre>";
    }
}
