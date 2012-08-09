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
                            $this->addVerificationMessage("Admin value attribute label is wrong.\n"
                                . 'Expected: ' . $value['admin_option_name'] . "\n"
                                . 'Actual: ' . $this->getValue($optionXpath));
                        }
                    } else {
                        $this->addVerificationMessage("Admin value attribute in $num row is not disabled");
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
            $this->addVerificationMessage('"Add Option" button is present');
        }
        if ($this->buttonIsPresent('delete_option')) {
            $this->addVerificationMessage('"Delete" button is present in Manage Options tab');
        }
        $this->processAttributeValue($attributeData, true);
    }

    /**
     * Creating new attribute on the product page with ability to assigned it to new attribute set
     *
     * @param array $attributeData
     * @param string $productTab
     * @param string $attributeSetName
     * @param bool $isNewAttributeSet
     */
    public function createAttributeOnProductPage(array $attributeData, $productTab, $attributeSetName = '',
        $isNewAttributeSet = false
    ) {
        //Defining and adding %fieldSetId% for Uimap pages.
        $fieldsetXpath = $this->_getControlXpath('fieldset', 'product_' . $productTab);
        if ($this->isElementPresent($fieldsetXpath)) {
            foreach (explode('_', $this->getAttribute($fieldsetXpath . '@id')) as $value) {
                if (is_numeric($value)) {
                    $this->addParameter('tabId', $value);
                    break;
                }
            }
        }
        if ($this->defineIdFromUrl() == null) {
            $isNew = true;
        } else {
            $isNew = false;
            $this->addParameter('productId', $this->defineIdFromUrl());
        }
        //Click 'Create New Attribute' button, select opened window.
        $this->clickButton('create_new_attribute', false);
        $names = $this->getAllWindowNames();
        $this->waitForPopUp(end($names), '30000');
        $this->selectWindow("name=" . end($names));
        if ($isNew) {
            $this->validatePage('new_product_attribute_from_product_page');
        } else {
            $this->validatePage('new_product_attribute_from_saved_product_page');
        }
        $this->fillTab($attributeData, 'properties', false);
        $this->fillTab($attributeData, 'manage_labels_options', false);
        $this->storeViewTitles($attributeData);
        $this->attributeOptions($attributeData);
        $this->addParameter('attributeId', 0);
        if ($isNewAttributeSet) {
            if ($this->controlIsPresent('button', 'save_in_new_attribute_set')) {
                $this->answerOnNextPrompt($attributeSetName);
                $this->clickButton('save_in_new_attribute_set', false);
                $this->getPrompt();
                $this->waitForPageToLoad();
                $newAttributeSetId = $this->defineParameterFromUrl('new_attribute_set_id');
                $this->clickButton('close_window', false);
                $this->selectWindowAndWait(null);
                $this->addParameter('setId', $newAttributeSetId);
                $this->waitForElement("//*[contains(@id,'" . $attributeData['attribute_code'] . "')]");
                $this->validatePage();
            }
        } else {
            $this->saveForm('save_attribute', false);
        }
    }
}
