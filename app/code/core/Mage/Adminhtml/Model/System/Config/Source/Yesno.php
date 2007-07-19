<?php

class Mage_Adminhtml_Model_System_Config_Source_Yesno
{
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>__('Yes')),
            array('value'=>0, 'label'=>__('No')),
        );
    }
}