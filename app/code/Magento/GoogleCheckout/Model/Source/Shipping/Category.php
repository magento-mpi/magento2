<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_GoogleCheckout_Model_Source_Shipping_Category implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'COMMERCIAL',  'label' => __('Commercial')),
            array('value' => 'RESIDENTIAL', 'label' => __('Residential')),
        );
    }
}
