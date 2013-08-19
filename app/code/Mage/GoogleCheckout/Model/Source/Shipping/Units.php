<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_GoogleCheckout_Model_Source_Shipping_Units
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'IN', 'label' => __('Inches')),
        );
    }
}
