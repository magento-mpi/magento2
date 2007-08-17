<?php

class Mage_Payment_Block_Info_Cc extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        $this->setTemplate('payment/info/ccsave.phtml');
        parent::_construct();
    }
    
}