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
 */
class Community2_Mage_AttributeSet_Helper extends Core_Mage_AttributeSet_Helper
{
    /**
     * Verifies attributes are assigned or not to attribute set
     *
     * @param array $assignedAttributes
     * @param array $unassignedAttributes
     */
    public function verifyAttributeAssignedToSet(array $assignedAttributes, array $unassignedAttributes = array())
    {
        if (!empty($assignedAttributes)) {
            foreach ($assignedAttributes as $attribute) {
                $this->addParameter('attributeName', $attribute);
                if (!$this->controlIsPresent('link', 'group_attribute')) {
                    $this->addVerificationMessage("Attribute with title '$attribute' is not assigned to attribute set");
                }
            }
        }
        if (!empty($unassignedAttributes)) {
            foreach ($unassignedAttributes as $attribute) {
                $this->addParameter('attributeName', $attribute);
                if (!$this->controlIsPresent('link', 'unassigned_attribute')) {
                    $this->addVerificationMessage("Attribute with title '$attribute' is assigned to attribute set");
                }
            }
        }
    }
}
