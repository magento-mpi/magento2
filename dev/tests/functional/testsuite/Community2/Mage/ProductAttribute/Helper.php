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
     * @param array $attrData
     */
    public function setDefaultAttributeValue(array $attrData)
    {
        $num = 1;
        $fieldSetXpath = $this->_getControlXpath('fieldset', 'manage_options');
        $option = $this->getXpathCount($fieldSetXpath . "//tr[contains(@class,'option-row')]");
        foreach (array_keys($attrData) as $key) {
            if (preg_match('/^option_/', $key) and is_array($attrData[$key])) {
                if ($this->controlIsPresent('fieldset', 'manage_options')) {
                    if ($option > 0) {
                        $fieldOptionNumber = $this->getAttribute(
                            $fieldSetXpath . "//tr[contains(@class,'option-row')][" . $num
                            . "]//input[@class='input-radio']/@value");
                        $this->addParameter('fieldOptionNumber', $fieldOptionNumber);
                        if ($attrData[$key]['admin_option_name'] == $attrData['default_value']) {
                            $this->fillFieldset(array('is_default' => 'Yes'), 'manage_options');
                        }
                        $num++;
                        $option--;
                    }
                }
            }
        }
    }

    /**
     * Verify dropdown system attribute on Manage Options tab: Manage Titles is present, Manage Options are present
     * and disabled, Delete and Add Option buttons are absent
     *
     * @param array $attrData
     */
    public function verifySystemAttribute($attrData)
    {
        $this->openTab('manage_labels_options');
        $this->storeViewTitles($attrData, 'manage_titles', 'verify');
        $this->assertFalse($this->buttonIsPresent('add_option'), 'It is possible to add new option');
        $this->assertFalse($this->buttonIsPresent('delete_option'), 'Delete button is present in Manage Options tab');
        $fieldSetXpath = $this->_getControlXpath('fieldset', 'manage_options');
        $option = $this->getXpathCount($fieldSetXpath . "//tr[contains(@class,'option-row')]");
        $num = 1;
        foreach (array_keys($attrData) as $key) {
            if (preg_match('/^option_/', $key) and is_array($attrData[$key])) {
                if ($this->controlIsPresent('fieldset', 'manage_options')) {
                    if ($option > 0) {
                        $fieldOptionNumber = $this->getAttribute(
                            $fieldSetXpath . "//tr[contains(@class,'option-row')][" . $num
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
