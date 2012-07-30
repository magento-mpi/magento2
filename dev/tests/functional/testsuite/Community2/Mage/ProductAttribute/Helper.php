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
     * Set default value for dropdown attribute
     *
     * @param array $attributeData
     */
    public function setDefaultAttributeValue(array $attributeData)
    {
        $num = 1;
        $fieldsetXpath = $this->_getControlXpath('fieldset', 'manage_options');
        $option = $this->getXpathCount($fieldsetXpath . "//tr[contains(@class,'option-row')]");
        foreach ($attributeData as $key => $value) {
            if (preg_match('/^option_/', $key) && is_array($value)
                && $this->controlIsPresent('fieldset', 'manage_options')
                && ($option > 0)
            ) {
                $fieldOptionNumber = $this->getAttribute($fieldsetXpath . "//tr[contains(@class,'option-row')][" . $num
                                                         . "]//input[@class='input-radio']/@value");
                $this->addParameter('fieldOptionNumber', $fieldOptionNumber);
                if ($value['admin_option_name'] == $attributeData['default_value']) {
                    $this->fillFieldset(array('is_default' => 'Yes'), 'manage_options');
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
        $fieldsetXpath = $this->_getControlXpath('fieldset', 'manage_options');
        $option = $this->getXpathCount($fieldsetXpath . "//tr[contains(@class,'option-row')]");
        $num = 1;
        foreach ($attributeData as $key => $value) {
            if (preg_match('/^option_/', $key) and is_array($value)) {
                if ($this->controlIsPresent('fieldset', 'manage_options')) {
                    if ($option > 0) {
                        $fieldOptionNumber = $this->getAttribute(
                            $fieldsetXpath . "//tr[contains(@class,'option-row')][" . $num
                            . "]//input[@class='input-radio']/@value");
                        $this->addParameter('fieldOptionNumber', $fieldOptionNumber);
                        $this->assertTrue($this->isElementPresent("//tr[contains(@class,'option-row')][" . $num
                            . "]//input[@class='input-text required-option' and @disabled='disabled']"),
                            'Admin value attribute is not disabled');
                        $num++;
                        $option--;
                    }
                }
            }
        }
    }
}
