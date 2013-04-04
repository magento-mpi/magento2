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
        $this->waitForControlNotVisible('fieldset', 'tax_rate_form');
    }

    /**
     * Edit Tax Rate on Tax Rule page
     *
     * @param string $rateName Tax Rate code to be edited
     * @param string|array $newTaxRateData New Tax Rate data
     */
    public function editTaxRate($rateName, $newTaxRateData)
    {
        $taxItemData = $this->fixtureDataToArray($newTaxRateData);
        $labelLocator = "//div[normalize-space(label/span)='%s']";
        $generalElement = $this->getControlElement(self::FIELD_TYPE_COMPOSITE_MULTISELECT, 'tax_rate');
        $optionElement = $this->getChildElement($generalElement, sprintf($labelLocator, $rateName));
        $optionElement->click();
        $this->moveto($optionElement);
        $this->getChildElement($optionElement, "//span[@title='Edit']")->click();
        $this->waitForControlVisible(self::UIMAP_TYPE_FIELDSET, 'tax_rate_form');
        $this->_fillTaxRateForm($taxItemData);
        $this->clickButton('save_rate', false);
        $this->waitForControlNotVisible(self::UIMAP_TYPE_FIELDSET, 'tax_rate_form');
        //Restore "Selected" state
        $rateName = ($newTaxRateData['tax_identifier'] && !empty($newTaxRateData['tax_identifier'])) ?
            $newTaxRateData['tax_identifier'] :
            $rateName;
        $this->getChildElement($generalElement, sprintf($labelLocator, $rateName))->click();
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