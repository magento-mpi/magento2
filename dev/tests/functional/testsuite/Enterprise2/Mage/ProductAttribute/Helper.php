<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ProductAttribute
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Helper class
 */
class Enterprise2_Mage_ProductAttribute_Helper extends Core_Mage_ProductAttribute_Helper
{
    /**
     * Set default value for dropdown attribute
     *
     * @param array $attrData
     */
    public function setDefaultAttributeValue(array $attrData)
    {
        $this->helper('Community2/Mage/ProductAttribute')->setDefaultAttributeValue($attrData);
    }

    /**
     * Verify dropdown system attribute on Manage Options tab:
     * Manage Titles is present, Manage Options are present and disabled,
     * Delete and Add Option buttons are absent
     *
     * @param array $attrData
     */
    public function verifySystemAttribute($attrData)
    {
        $this->helper('Community2/Mage/ProductAttribute')->verifySystemAttribute($attrData);
    }
}
