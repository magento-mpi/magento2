<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Shipping_Model_Config_Source_Allspecificcountries implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value'=>0, 'label'=>__('All Allowed Countries')),
            array('value'=>1, 'label'=>__('Specific Countries')),
        );
    }
}
