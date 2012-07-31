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
     * @param bool $isVerify
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * ($isVerify variable is actually used but PHPMD produces false alarm)
     */
    public function setDefaultAttributeValue(array $attributeData, $isVerify = false)
    {
        $num = 1;
        $fieldsetXpath = $this->_getControlXpath('fieldset', 'manage_options');
        $option = $this->getXpathCount($fieldsetXpath . "//tr[contains(@class,'option-row')]");
        foreach ($attributeData as $key => $value) {
            if (preg_match('/^option_/', $key) && is_array($value)
                && $this->controlIsPresent('fieldset', 'manage_options')
                && $option > 0
            ) {
                $fieldOptionNumber = $this->getAttribute($fieldsetXpath . "//tr[contains(@class,'option-row')][" . $num
                    . "]//input[@class='input-radio']/@value");
                $this->addParameter('fieldOptionNumber', $fieldOptionNumber);
                if ($isVerify) {
                    $optionXpath = "//tr[contains(@class,'option-row')][" . $num
                        . "]//input[@class='input-text required-option' and @disabled='disabled']";
                    $this->assertTrue($this->isElementPresent($optionXpath), 'Admin value attribute is not disabled');
                    $this->assertEquals($value['admin_option_name'], $this->getValue($optionXpath));
                } elseif ($value['admin_option_name'] == $attributeData['default_value']) {
                    $this->fillCheckbox('is_default', 'Yes');
                }
                $num++;
                $option--;
            }
        }
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
        $this->assertFalse($this->buttonIsPresent('add_option'), 'It is possible to add new option');
        $this->assertFalse($this->buttonIsPresent('delete_option'), 'Delete button is present in Manage Options tab');
        $this->setDefaultAttributeValue($attributeData, true);
    }
}
