<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_GoogleCheckout_Model_Source_Shipping_Carrier
{
    public function toOptionArray()
    {
        return array(
            array('label' => __('FedEx'), 'value' => array(
                array('label' => __('Ground'), 'value' => 'FedEx/Ground'),
                array('label' => __('Home Delivery'), 'value' => 'FedEx/Home Delivery'),
                array('label' => __('Express Saver'), 'value' => 'FedEx/Express Saver'),
                array('label' => __('First Overnight'), 'value' => 'FedEx/First Overnight'),
                array('label' => __('Priority Overnight'), 'value' => 'FedEx/Priority Overnight'),
                array('label' => __('Standard Overnight'), 'value' => 'FedEx/Standard Overnight'),
                array('label' => __('2Day'), 'value' => 'FedEx/2Day'),
            )),
            array('label' => __('UPS'), 'value' => array(
                array('label' => __('Next Day Air'), 'value' => 'UPS/Next Day Air'),
                array('label' => __('Next Day Air Early AM'), 'value' => 'UPS/Next Day Air Early AM'),
                array('label' => __('Next Day Air Saver'), 'value' => 'UPS/Next Day Air Saver'),
                array('label' => __('2nd Day Air'), 'value' => 'UPS/2nd Day Air'),
                array('label' => __('2nd Day Air AM'), 'value' => 'UPS/2nd Day Air AM'),
                array('label' => __('3 Day Select'), 'value' => 'UPS/3 Day Select'),
                array('label' => __('Ground'), 'value' => 'UPS/Ground'),
            )),
            array('label' => __('USPS'), 'value' => array(
                array('label' => __('Express Mail'), 'value' => 'USPS/Express Mail'),
                array('label' => __('Priority Mail'), 'value' => 'USPS/Priority Mail'),
                array('label' => __('Parcel Post'), 'value' => 'USPS/Parcel Post'),
                array('label' => __('Media Mail'), 'value' => 'USPS/Media Mail'),
            )),
        );
    }
}
