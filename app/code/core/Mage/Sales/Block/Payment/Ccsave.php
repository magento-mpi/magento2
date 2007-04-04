<?php

class Mage_Sales_Block_Payment_Ccsave extends Mage_Core_Block_Template 
{
    function __construct()
    {
        parent::__construct();
        
        $this->setViewName('Mage_Sales', '')
    }
}