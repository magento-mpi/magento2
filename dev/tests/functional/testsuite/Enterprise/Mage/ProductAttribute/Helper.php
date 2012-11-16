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
 * @method Community2_Mage_ProductAttribute_Helper   helper(string $className)
 */
class Enterprise_Mage_ProductAttribute_Helper extends Core_Mage_ProductAttribute_Helper
{

    /**
     * Set default value for dropdown attribute and verify admin values if $isCheck = true
     *
     * @param array $attributeData
     * @param bool $isCheck
     * @param bool $setDefaultValue
     *
     * @return array
     */
    public function processAttributeValue(array $attributeData, $isCheck = false, $setDefaultValue = false)
    {
        return $this->helper('Community2/Mage/ProductAttribute/Helper')
            ->processAttributeValue($attributeData, $isCheck, $setDefaultValue);
    }

    /**
     * Verify whether product has custom option
     *
     * @param $key
     * @param $value
     * @param $option
     *
     * @return bool
     */
    protected function _hasOptions($key, $value, $option)
    {
        $this->helper('Community2/Mage/ProductAttribute/Helper')->_hasOptions($key, $value, $option);
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
        $this->helper('Community2/Mage/ProductAttribute/Helper')->verifySystemAttribute($attributeData);
    }

    /**
     * Edit product attribute
     *
     * @param string $attributeCode
     * @param array $editedData
     */
    public function editAttribute($attributeCode, array $editedData)
    {
        $this->helper('Community2/Mage/ProductAttribute/Helper')->editAttribute($attributeCode, $editedData);
    }
}
