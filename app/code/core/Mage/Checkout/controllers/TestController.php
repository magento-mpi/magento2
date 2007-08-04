<?php

class Mage_Checkout_TestController extends Mage_Core_Controller_Front_Action 
{
    public function indexAction()
    {
        $quote = Mage::getModel('sales/quote');
        echo "<pre>".print_r($quote,1)."</pre>";
        
    }
    
    public function createEntitiesAction()
    {
        $setup = Mage::getModel('sales_entity/setup', 'sales_setup');
        $setup->installEntities($setup->getDefaultEntities());
    }
}