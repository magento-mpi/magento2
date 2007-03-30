<?php

class Mage_Checkout_IndexController extends Mage_Core_Controller_Front_Action 
{
    function indexAction()
    {
        // check customer auth
/*        if (!Mage_Customer_Front::authenticate($this)) {
            return;
        }*/

        $this->_redirect(Mage::getbaseUrl('', 'Mage_Checkout').'/onepage/');
    }
}