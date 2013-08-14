<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_GoogleCheckout_Model_Source_Checkout_Image
{
    public function toOptionArray()
    {
        $sizes = array(
            '180/46' => Mage::helper('Magento_GoogleCheckout_Helper_Data')->__('Large - %s', '180x46'),
            '168/44' => Mage::helper('Magento_GoogleCheckout_Helper_Data')->__('Medium - %s', '168x44'),
            '160/43' => Mage::helper('Magento_GoogleCheckout_Helper_Data')->__('Small - %s', '160x43'),
        );

        $styles = array(
            'trans' => Mage::helper('Magento_GoogleCheckout_Helper_Data')->__('Transparent'),
            'white' => Mage::helper('Magento_GoogleCheckout_Helper_Data')->__('White Background'),
        );

        $options = array();
        foreach ($sizes as $size => $sizeLabel) {
            foreach ($styles as $style => $styleLabel) {
                $options[] = array(
                    'value' => $size . '/' . $style,
                    'label' => $sizeLabel . ' (' . $styleLabel . ')'
                );
            }
        }

        return $options;
    }
}
