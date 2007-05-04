<?php

class Mage_Checkout_IndexController extends Mage_Core_Controller_Front_Action 
{
    function indexAction()
    {
        // check customer auth
/*        if (!Mage_Customer_Front::authenticate($this)) {
            return;
        }*/
        Mage::getSingleton('customer', 'session')->authenticate($this);
        $this->getResponse()->setRedirect(Mage::getUrl('checkout', array('controller'=>'onepage', '_secure'=>true)));
    }
}