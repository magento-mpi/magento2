<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_GoogleCheckout_Model_Source_Locale
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'en_US', 'label'=>Mage::helper('Magento_GoogleCheckout_Helper_Data')->__('United States')),
            array('value' => 'en_GB', 'label'=>Mage::helper('Magento_GoogleCheckout_Helper_Data')->__('United Kingdom')),
        );
    }
}
