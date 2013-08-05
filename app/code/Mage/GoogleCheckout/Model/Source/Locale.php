<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_GoogleCheckout_Model_Source_Locale
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'en_US', 'label'=>__('United States')),
            array('value' => 'en_GB', 'label'=>__('United Kingdom')),
        );
    }
}
