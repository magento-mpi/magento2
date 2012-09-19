<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Product
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Helper class
 *
 */
class Community2_Mage_Product_Helper extends Core_Mage_Product_Helper
{
    /**
     * Change Attribute Set
     *
     * @param array $newAttributeSet
     */
    public function changeAttributeSet($newAttributeSet)
    {
        $currentAttributeSet = $this->getSelectedValue($this->_getControlXpath('dropdown', 'choose_attribute_set'));
        $fieldXpath = $this->_getControlXpath('pageelement', 'product_page_title');
        $popupXpath = $this->_getControlXpath('fieldset', 'change_attribute_set');
        $actualTitle = $this->getText($fieldXpath);
        $newTitle = str_replace($newAttributeSet, $currentAttributeSet, $actualTitle);
        $this->clickButton('change_attribute_set', false);
        $this->waitForElement($popupXpath);
        $this->fillDropdown('choose_attribute_set', $newAttributeSet);
        $this->addParameter('setId',
            $this->getSelectedValue($this->_getControlXpath('dropdown', 'choose_attribute_set')));
        $this->clickButton('apply');
        $this->validatePage();
        if ($this->getText($fieldXpath) == $newTitle) {
            $this->assertSame($this->getText($fieldXpath),
                $newTitle, "Attribute set in title should be $newAttributeSet, but now it's $currentAttributeSet") ;
        }
    }
}
