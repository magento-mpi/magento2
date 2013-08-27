<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Shipping_Model_Source_HandlingType
{
    public function toOptionArray()
    {
        return array(
            array('value' => Magento_Shipping_Model_Carrier_Abstract::HANDLING_TYPE_FIXED, 'label' => __('Fixed')),
            array('value' => Magento_Shipping_Model_Carrier_Abstract::HANDLING_TYPE_PERCENT, 'label' => __('Percent')),
        );
    }
}
