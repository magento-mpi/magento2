<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Shipping_Model_Source_HandlingAction
{
    public function toOptionArray()
    {
        return array(
            array('value' => Mage_Shipping_Model_Carrier_Abstract::HANDLING_ACTION_PERORDER, 'label' => __('Per Order')),
            array('value' => Mage_Shipping_Model_Carrier_Abstract::HANDLING_ACTION_PERPACKAGE , 'label' => __('Per Package')),
        );
    }
}
