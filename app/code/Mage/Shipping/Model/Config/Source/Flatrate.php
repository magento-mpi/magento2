<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Shipping_Model_Config_Source_Flatrate implements Mage_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'', 'label'=> __('None')),
            array('value'=>'O', 'label'=>__('Per Order')),
            array('value'=>'I', 'label'=>__('Per Item')),
        );
    }
}
