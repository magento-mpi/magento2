<?php

class Mage_Adminhtml_Model_System_Config_Source_Shipping_Flatrate
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'O', 'label'=>__('Per Order')),
            array('value'=>'I', 'label'=>__('Per Item')),
        );
    }
}
