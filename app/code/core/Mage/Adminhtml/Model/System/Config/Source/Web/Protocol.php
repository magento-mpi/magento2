<?php

class Mage_Adminhtml_Model_System_Config_Source_Web_Protocol
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'http', 'label'=>__('HTTP (unsecure)')),
            array('value'=>'https', 'label'=>__('HTTPS (SSL)')),
        );
    }
}