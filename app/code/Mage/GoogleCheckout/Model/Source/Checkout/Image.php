<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_GoogleCheckout_Model_Source_Checkout_Image
{
    public function toOptionArray()
    {
        $sizes = array(
            '180/46' => __('Large - %1', '180x46'),
            '168/44' => __('Medium - %1', '168x44'),
            '160/43' => __('Small - %1', '160x43'),
        );

        $styles = array(
            'trans' => __('Transparent'),
            'white' => __('White Background'),
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
