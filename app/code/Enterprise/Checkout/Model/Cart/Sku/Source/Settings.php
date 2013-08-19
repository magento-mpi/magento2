<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Enable Order by SKU in 'My Account' options source
 *
 * @category   Enterprise
 * @package    Enterprise_Checkout
 */
class Enterprise_Checkout_Model_Cart_Sku_Source_Settings
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
