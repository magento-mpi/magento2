<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_GoogleCheckout_Model_Source_Shipping_Category
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'COMMERCIAL',  'label' => __('Commercial')),
            array('value' => 'RESIDENTIAL', 'label' => __('Residential')),
        );
    }
}
