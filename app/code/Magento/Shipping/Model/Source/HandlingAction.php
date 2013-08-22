<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Shipping_Model_Source_HandlingAction
{
    public function toOptionArray()
    {
        return array(
            array('value' => Magento_Shipping_Model_Carrier_Abstract::HANDLING_ACTION_PERORDER, 'label' => __('Per Order')),
            array('value' => Magento_Shipping_Model_Carrier_Abstract::HANDLING_ACTION_PERPACKAGE , 'label' => __('Per Package')),
        );
    }
}
