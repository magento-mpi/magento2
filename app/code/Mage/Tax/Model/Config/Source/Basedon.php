<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Tax_Model_Config_Source_Basedon implements Mage_Core_Model_Option_ArrayInterface
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
