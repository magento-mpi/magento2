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
class Community2_Mage_ProductAttribute_Helper extends Core_Mage_ProductAttribute_Helper
{
    /**
     * Set default value for dropdown attribute and verify admin values if $isVerify = true
     *
     * @param array $attributeData
     * @param bool $isCheck
     */
    public function processAttributeValue(array $attributeData, $isCheck = false)
    {
        $num = 1;
        $fieldsetXpath = $this->_getControlXpath('fieldset', 'manage_options');
        $option = $this->getXpathCount($fieldsetXpath . "//tr[contains(@class,'option-row')]");
        foreach ($attributeData as $key => $value) {
            if (preg_match('/^option_/', $key) && is_array($value)
                && $this->controlIsPresent('fieldset', 'manage_options')
                && $option > 0
            ) {
                $this->addParameter('rowNumber', $num);
                if ($isCheck) {
                    if ($this->controlIsPresent('field', 'admin_option_name_disabled')) {
                        $optionXpath = $this->_getControlXpath('field', 'admin_option_name_disabled');
                        if ($value['admin_option_name'] != $this->getValue($optionXpath)) {
                            $this->addVerificationMessage("Admin value attribute label is wrong.\nExpected: "
                                . $value['admin_option_name'] . "\nActual: " . $this->getValue($optionXpath));
                        }
                    } else {
                        $this->addVerificationMessage("Admin value attribute  in $num row is not disabled");
                    }
                } elseif ($value['admin_option_name'] == $attributeData['default_value']) {
                    $this->fillCheckbox('default_value', 'Yes');
                }
                $num++;
                $option--;
            }
        }
        $this->assertEmptyVerificationErrors();
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
        $this->openTab('manage_labels_options');
        $this->storeViewTitles($attributeData, 'manage_titles', 'verify');
        if ($this->buttonIsPresent('add_option')) {
            $this->addVerificationMessage('It is possible to add new option');
        }
        if ($this->buttonIsPresent('delete_option')) {
            $this->addVerificationMessage('Delete button is present in Manage Options tab');
        }
        $this->processAttributeValue($attributeData, true);
    }
}
