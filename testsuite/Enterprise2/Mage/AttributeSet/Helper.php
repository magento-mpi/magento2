<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_AttributeSet
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Helper class
 * @method Community2_Mage_AttributeSet_Helper   helper(string $className)
 */
class Enterprise2_Mage_AttributeSet_Helper extends Core_Mage_AttributeSet_Helper
{
    /**
     * Verifies attributes are assigned or not to attribute set
     *
     * @param array $attributes
     * @param bool $isAssigned
     */
    public function verifyAttributeAssignment(array $attributes, $isAssigned = true)
    {
        $this->helper('Community2/Mage/AttributeSet/Helper')->verifyAttributeAssignment($attributes, $isAssigned);
    }

    /**
     * Delete attribute from attribute Set
     *
     * @param array $attributes Array which contains attributes for unassignment
     * @param bool $isConfirmation Verification for alert message
     */
    public function unassignAttributeFromSet(array $attributes, $isConfirmation = false)
    {
        $this->helper('Community2/Mage/AttributeSet/Helper')->unassignAttributeFromSet($attributes, $isConfirmation);
    }

    /**
     * Delete group from attribute set
     *
     * @param array $attributeGroup Array which contains groups to delete
     * @param bool $isCorrectConfirmation Verification for alert message
     */
    public function deleteGroup($attributeGroup, $isCorrectConfirmation = true)
    {
        $this->helper('Community2/Mage/AttributeSet/Helper')->unassignAttributeFromSet($attributeGroup,
            $isCorrectConfirmation);
    }
}
