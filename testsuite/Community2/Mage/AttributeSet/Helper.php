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
     * @param array $attributes
     * @param bool $isAssigned
     */
    public function verifyAttributeAssignment(array $attributes, $isAssigned = true)
    {
        if (empty($attributes)) {
            $this->fail('Array with attributes is empty');
        }
        foreach ($attributes as $attribute) {
            $this->addParameter('attributeName', $attribute);
            if ($isAssigned) {
                if (!$this->controlIsPresent('link', 'group_attribute')) {
                    $this->addVerificationMessage("Attribute with title '$attribute' is not assigned to attribute set");
                }
            } else {
                if (!$this->controlIsPresent('link', 'unassigned_attribute')) {
                    $this->addVerificationMessage("Attribute with title '$attribute' is assigned to attribute set");
                }
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * Delete attribute from attribute Set
     *
     * @param array $attributes Array which contains attributes for unassignment
     * @param bool $isConfirmation Verification for alert message
     */
    public function unassignAttributeFromSet(array $attributes, $isConfirmation = false)
    {
        foreach ($attributes as $attributeCode) {
            $this->addParameter('attributeName', $attributeCode);
            $elFrom = $this->_getControlXpath('link', 'group_attribute');
            $elTo = $this->_getControlXpath('pageelement', 'node_icon');
            if (!$this->isElementPresent($elTo)) {
                $this->fail("First element was not found in Unassigned Attributes section");
            }
            if (!$this->isElementPresent($elFrom)) {
                $this->fail("Attribute with code '$attributeCode' does not exist");
            }
            $this->moveElementOverTree('link', 'group_attribute', 'fieldset', 'groups_content');
            $this->moveElementOverTree('pageelement', 'node_icon', 'fieldset', 'unassigned_attributes');
            $this->mouseDownAt($elFrom, '1,1');
            $this->mouseMoveAt($elTo, '1,1');
            $this->mouseUpAt($elTo, '1,1');
            if ($isConfirmation && $this->isAlertPresent()) {
                $text = $this->getAlert();
                if ($text != $this->getCurrentUimapPage()->findMessage('remove_system_attribute')) {
                    $this->addVerificationMessage('The alert text is incorrect: ' . $text);
                }
            }
        }
    }

    /**
     * Delete group from attribute set
     *
     * @param array $attributeGroup Array which contains groups to delete
     * @param bool $isConfirmation Verification for alert message
     */
    public function deleteGroup($attributeGroup, $isConfirmation = false)
    {
        foreach ($attributeGroup as $value) {
            $this->addParameter('folderName', $value);
            if ($this->controlIsPresent('link', 'group_folder')) {
                $this->clickControl('link', 'group_folder', false);
                $this->clickButton('delete_group', false);
                if ($isConfirmation && $this->isAlertPresent()) {
                    $text = $this->getAlert();
                    if ($text != $this->getCurrentUimapPage()->findMessage('delete_group')) {
                        $this->addVerificationMessage('The alert text is incorrect: ' . $text);
                    }
                }
            } else {
                $this->addVerificationMessage('Group ' . $value . 'does not exist');
            }
        }
        $this->assertEmptyVerificationErrors();
    }
}
