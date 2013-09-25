<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_GoogleCheckout_Model_Source_Shipping_Virtual_Method implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'email', 'label' => __('Email delivery')),
            // array('value'=>'key_url', 'label'=> __('Key/URL delivery')),
            // array('value'=>'description_based', 'label'=> __('Description-based delivery'))
        );
    }
}
