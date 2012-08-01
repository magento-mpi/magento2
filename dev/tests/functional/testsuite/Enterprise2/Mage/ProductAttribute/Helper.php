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
class Enterprise2_Mage_ProductAttribute_Helper extends Community2_Mage_ProductAttribute_Helper
{
    /**
     * Set default value for dropdown attribute and verify admin values if $isVerify = true
     *
     * @param array $attributeData
     * @param bool $isCheck
     */
    public function processAttributeValue(array $attributeData, $isCheck = false)
    {
        parent::processAttributeValue($attributeData, $isCheck);
    }

    /**
     * Verify dropdown system attribute on Manage Options tab:
     * Manage Titles is present, Manage Options are present and disabled,
     * Delete and Add Option buttons are absent
     *
     * @param array $attributeData
     */
    public function verifySystemAttribute($attributeData)
    {
        parent::verifySystemAttribute($attributeData);
    }
}
