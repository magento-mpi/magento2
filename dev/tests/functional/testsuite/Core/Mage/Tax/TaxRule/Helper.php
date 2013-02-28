<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_StoreLauncher
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Tax_TaxRule_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Create Tax Rate on Tax Rule page
     *
     * @param $taxRateData
     */
    public function createTaxRate($taxRateData)
    {
        $taxItemData = $this->fixtureDataToArray($taxRateData);
        $this->clickButton('add_rate', false);
        $this->waitForElementVisible($this->_getControlXpath('fieldset', 'tax_rate_form'));
        $this->_fillTaxRateForm($taxItemData);
        $this->clickButton('save_rate', false);
        $this->waitForElementInvisible($this->_getControlXpath('fieldset', 'tax_rate_form'));
    }

    /**
     * Edit Tax Rate on Tax Rule page
     *
     * @param $rateName Tax Rate code to be edited
     * @param $newTaxRateData New Tax Rate data
     */
    public function editTaxRate($rateName, $newTaxRateData)
    {
        $taxItemData = $this->fixtureDataToArray($newTaxRateData);
        $locator = $this->_getControlXpath(self::FIELD_TYPE_COMPOSITE_MULTISELECT, 'tax_rate');
        $labelLocator = "//div[normalize-space(label/span)='%s']";
        $generalElement = $this->getElement($locator);
        $optionElement = $this->getChildElement($generalElement, sprintf($labelLocator, $rateName));
        $optionElement->click();
        $this->getChildElement($optionElement, "//span[@title='Edit']")->click();
        $this->waitForElementVisible($this->_getControlXpath('fieldset', 'tax_rate_form'));
        $this->_fillTaxRateForm($taxItemData);
        $this->clickButton('save_rate', false);
        $this->waitForElementInvisible($this->_getControlXpath('fieldset', 'tax_rate_form'));
    }

    /**
     * Fill Tax Rate form fields
     *
     * @param $taxItemData
     */
    protected function _fillTaxRateForm($taxItemData)
    {
        $this->fillFieldset($taxItemData, 'tax_rate_form');
        $rateTitles = (isset($taxItemData['tax_titles'])) ? $taxItemData['tax_titles'] : array();
        if ($rateTitles) {
            $this->assertTrue($this->controlIsPresent('fieldset', 'tax_titles'),
                'Tax Titles for store views are defined, but cannot be set.');
            foreach ($rateTitles as $key => $value) {
                $this->addParameter('storeName', $key);
                $this->fillField('tax_title', $value);
            }
        }
    }
}