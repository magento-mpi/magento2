<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Tax_Model_Config_Source_Basedon implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'shipping', 'label'=>__('Shipping Address')),
            array('value'=>'billing', 'label'=>__('Billing Address')),
            array('value'=>'origin', 'label'=>__("Shipping Origin")),
        );
    }

}
