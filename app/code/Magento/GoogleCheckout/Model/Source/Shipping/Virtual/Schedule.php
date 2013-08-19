<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_GoogleCheckout_Model_Source_Shipping_Virtual_Schedule
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'OPTIMISTIC',  'label' => __('Optimistic')),
            array('value' => 'PESSIMISTIC', 'label' => __('Pessimistic')),
        );
    }
}
