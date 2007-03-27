<?php

class Mage_Checkout_IndexController extends Mage_Core_Controller_Front_Action 
{
    function indexAction()
    {
        $this->_redirect(Mage::getbaseUrl('', 'Mage_Checkout').'/standard');

    }
}