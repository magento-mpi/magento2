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
class Enterprise2_Mage_AttributeSet_Helper extends Community2_Mage_AttributeSet_Helper
{
    /**
     * Verifies attributes are assigned or not to attribute set
     *
     * @param array $assignedAttributes
     * @param array $unassignedAttributes
     */
    public function verifyAttributeAssignedToSet(array $assignedAttributes, array $unassignedAttributes = array())
    {
        parent::verifyAttributeAssignedToSet($assignedAttributes, $unassignedAttributes);
    }
}
