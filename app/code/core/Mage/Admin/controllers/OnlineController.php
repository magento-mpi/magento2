<?php

class Mage_Admin_OnlineController extends Mage_Core_Controller_Front_Action
{

    public function IndexAction()
    {
        $collection = Mage::getSingleton('log_resource/online_collection')
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