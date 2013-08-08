<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Payment_Model_Config_Source_Allspecificcountries implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value'=>0, 'label'=>Mage::helper('Mage_Payment_Helper_Data')->__('All Allowed Countries')),
            array('value'=>1, 'label'=>Mage::helper('Mage_Payment_Helper_Data')->__('Specific Countries')),
        );
    }
}
