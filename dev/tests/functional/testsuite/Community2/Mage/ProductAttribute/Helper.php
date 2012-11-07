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
        $options = array();
        $isSetDefault = false;
        $this->openTab('manage_labels_options');
        $optionLines = $this->getControlElements('pageelement', 'option_line');
        $optionCount = count($optionLines);
        $i = 0;
        foreach ($attributeData as $key => $value) {
            if ($this->_hasOptions($key, $value, $optionCount)) {
                $options[$i++] = $value;
                $optionCount--;
                unset($attributeData[$key]);
            }
        }
        $locator = "//input[@class='input-text required-option' and @disabled='disabled']";
        /**
         * @var PHPUnit_Extensions_Selenium2TestCase_Element $optionLine
         */
        foreach ($optionLines as $key => $optionLine) {
            $admin = $this->getChildElement($optionLine, $locator);
            $currentValue = trim($admin->value());
            $this->addParameter('rowNumber', $key + 1);
            if ($isCheck) {
                if (!isset($options[$key]) || !isset($options[$key]['admin_option_name'])) {
                    $this->addVerificationMessage('Admin Option Name for option with index ' . $key
                                                  . ' is not set. Exist more options than specified.');
                    continue;
                }
                $expectedValue = $options[$key]['admin_option_name'];
                if ($this->controlIsPresent('field', 'admin_option_name_disabled')) {
                    if ($expectedValue != $currentValue) {
                        $this->addVerificationMessage(
                            "Admin value attribute label is wrong.\nExpected: " . $options[$key]['admin_option_name']
                            . "\nActual: " . $currentValue);
                    }
                } else {
                    $this->addVerificationMessage('Admin value attribute in ' . $key . ' row is not disabled');
                }
            }
            if ($setDefaultValue && isset($attributeData['default_value'])
                && $attributeData['default_value'] == $currentValue
            ) {
                $this->addParameter('optionName', $currentValue);
                $this->fillCheckbox('default_value_by_option_name', 'Yes');
                $isSetDefault = true;
                $setDefaultValue = false;
            }
        }
        if ($isSetDefault == false && $setDefaultValue) {
            $this->addVerificationMessage('Default option can not be set as it does not exist');
        }
        $this->assertEmptyVerificationErrors();
        return $attributeData;
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
        return preg_match('/^option_/', $key) && is_array($value)
               && $this->controlIsPresent('fieldset', 'manage_options')
               && $option > 0;
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
        $setDeFaultValue = isset($attributeData['default_value']);
        $dataWithoutOptions = $this->processAttributeValue($attributeData, true, $setDeFaultValue);
        $this->storeViewTitles($dataWithoutOptions, 'manage_titles', 'verify');
        if ($this->buttonIsPresent('add_option')) {
            $this->addVerificationMessage('"Add Option" button is present');
        }
        if ($this->buttonIsPresent('delete_option')) {
            $this->addVerificationMessage('"Delete" button is present in Manage Options tab');
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * Edit product attribute
     *
     * @param string $attributeCode
     * @param array $editedData
     */
    public function editAttribute($attributeCode, array $editedData)
    {
        $this->openAttribute(array('attribute_code' => $attributeCode));
        $this->fillTab($editedData, 'properties', false);
        if (!$this->fillTab($editedData, 'manage_labels_options', false)) {
            $this->openTab('manage_labels_options');
        }
        $this->storeViewTitles($editedData);
        $this->attributeOptions($editedData);
        $this->saveForm('save_attribute', false);
    }
}
