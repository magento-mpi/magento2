<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Shipping_Model_Config_Source_Flatrate implements Magento_Core_Model_Option_ArrayInterface
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
