<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Enable Order by SKU in 'My Account' options source
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 */
class Magento_AdvancedCheckout_Model_Cart_Sku_Source_Settings implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Enable Order by SKU in 'My Account' options values
     */
    const NO_VALUE = 0;
    const YES_VALUE = 1;
    const YES_SPECIFIED_GROUPS_VALUE = 2;

    /**
     * Get options as array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'label' => __('Yes, for Specified Customer Groups'),
                'value' => self::YES_SPECIFIED_GROUPS_VALUE
            ),
            array(
                'label' => __('Yes, for Everyone'),
                'value' => self::YES_VALUE
            ),
            array(
                'label' => __('No'),
                'value' => self::NO_VALUE
            ),
        );
    }
}
