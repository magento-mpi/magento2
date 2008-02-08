<?php

class Mage_Adminhtml_Model_Url extends Mage_Core_Model_Url
{
    public function getSecure()
    {
        return Mage::getStoreConfigFlag('web/secure/use_in_adminhtml');
    }
}