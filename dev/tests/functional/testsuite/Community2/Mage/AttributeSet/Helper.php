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
     * @param bool $isVerifyConfirmation Verification for alert message
     */
    public function unassignAttributeFromSet(array $attributes, $isVerifyConfirmation = false)
    {
        foreach ($attributes as $attributeCode) {
            $this->addParameter('attributeName', $attributeCode);
            $this->clickControl('link', 'group_attribute', false);
            $this->moveto($this->getControlElement('link', 'group_attribute'));
            $this->buttondown();
            $this->moveto($this->getControlElement('pageelement', 'node_icon'));
            $this->buttonup();
            if ($this->alertIsPresent()) {
                $text = $this->alertText();
                $this->acceptAlert();
                $this->assertFalse($this->controlIsPresent('link', 'unassigned_attribute'));
                if ($isVerifyConfirmation
                    && $text != $this->getCurrentUimapPage()->findMessage('remove_system_attribute')
                ) {
                    $this->addVerificationMessage('The alert text is incorrect: ' . $text);
                }
            } else {
                $this->assertTrue($this->controlIsPresent('link', 'unassigned_attribute'));
            }
        }
    }

    /**
     * Delete group from attribute set
     *
     * @param array $attributeGroup Array which contains groups to delete
     * @param bool $isCorrectConfirmation Verification for alert message
     */
    public function deleteGroup($attributeGroup, $isCorrectConfirmation = true)
    {
        foreach ($attributeGroup as $value) {
            $this->addParameter('folderName', $value);
            if ($this->controlIsPresent('link', 'group_folder')) {
                $this->clickControl('link', 'group_folder', false);
                $this->clickButton('delete_group', false);
                if ($this->alertIsPresent()) {
                    $text = $this->alertText();
                    $this->acceptAlert();
                    if ($isCorrectConfirmation) {
                        if ($text != $this->getCurrentUimapPage()->findMessage('delete_group')) {
                            $this->addVerificationMessage('The alert text is incorrect: ' . $text);
                        }
                    }
                }
            } else {
                $this->addVerificationMessage('Group ' . $value . 'does not exist');
            }
        }
        $this->assertEmptyVerificationErrors();
    }
}
