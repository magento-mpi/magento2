<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Tax
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
class Core_Mage_Tax_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Create Product Tax Rule
     *
     * @param array|string $taxItemData
     * @param string $type search type rule
     */
    public function createTaxItem($taxItemData, $type)
    {
        $taxItemData = $this->fixtureDataToArray($taxItemData);
        $this->clickButton('add_' . $type);
        $this->fillFieldset($taxItemData, 'tax_rule_info', false);
        $this->clickControl('link', 'tax_rule_info_additional_link');
        $this->fillFieldset($taxItemData, 'tax_rule_info_additional', false);
        $this->saveForm('save_' . $type);
    }

    /**
     * Create Product Tax Rule
     *
     * @param array|string $taxItemData
     */
    public function createTaxRule($taxItemData)
    {
        $taxItemData = $this->fixtureDataToArray($taxItemData);
        $this->clickButton('add_rule');
        $this->fillFieldset($taxItemData, 'tax_rule_info', false);
        $this->clickControl('link', 'tax_rule_info_additional_link');
        $this->fillFieldset($taxItemData, 'tax_rule_info_additional', false);
        $this->saveForm('save_rule');
    }

    /**
     * Create Product Tax Class|Customer Tax Class
     *
     * @param $taxClassData
     */
    public function createTaxClass($taxClassData)
    {
        $taxItemData = $this->fixtureDataToArray($taxClassData);
        $this->clickButton('add_rule');
        $this->clickControl('link', 'tax_rule_info_additional_link');
        $this->fillFieldset($taxItemData, 'tax_rule_info_additional', false);
    }

    /**
     * Create Tax Rate
     *
     * @param $taxRateData
     */
    public function createTaxRate($taxRateData)
    {
        $taxItemData = $this->fixtureDataToArray($taxRateData);
        $this->clickButton('add_rate');
        $this->fillFieldset($taxItemData, 'tax_rate_info');
        $rateTitles = (isset($taxItemData['tax_titles'])) ? $taxItemData['tax_titles'] : array();
        if ($rateTitles) {
            $this->assertTrue($this->controlIsPresent('fieldset', 'tax_titles'),
                'Tax Titles for store views are defined, but cannot be set.');
            foreach ($rateTitles as $key => $value) {
                $this->addParameter('storeName', $key);
                $this->fillField('tax_title', $value);
            }
        }
        $this->saveForm('save_rate');
    }

    /**
     * Open Tax Rate|Tax Rule
     *
     * @param array $searchData Data for search
     * @param string $type search type rate|rule
     *
     * @throws OutOfRangeException
     */
    public function openTaxItem(array $searchData, $type)
    {
        $searchData = $this->_prepareDataForSearch($searchData);
        $taxLocator = $this->search($searchData, 'manage_tax_' . $type);
        $this->assertNotNull($taxLocator, 'Search item is not found with data: ' . print_r($searchData, true));
        switch ($type) {
            case 'rate':
                $cellId = $this->getColumnIdByName('Tax Identifier');
                break;
            case 'rule':
                $cellId = $this->getColumnIdByName('Name');
                break;
            default:
                throw new OutOfRangeException('Unsupported value for parameter $type');
                break;
        }
        $taxRowElement = $this->getElement($taxLocator);
        $taxUrl = $taxRowElement->attribute('title');
        $taxElement = $this->getChildElement($taxRowElement, 'td[' . $cellId . ']');
        $this->addParameter('elementTitle', trim($taxElement->text()));
        $this->addParameter($type, $this->defineParameterFromUrl($type, $taxUrl));
        $this->url($taxUrl);
        $this->validatePage();
    }

    /**
     * Delete Product Tax Class|Customer Tax Class|Tax Rate|Tax Rule
     *
     * @param array $taxSearchData Data for search
     * @param string $type search type rate|rule|customer_class|product_class
     */
    public function deleteTaxItem(array $taxSearchData, $type)
    {
        $this->openTaxItem($taxSearchData, $type);
        $this->clickButtonAndConfirm('delete_' . $type, 'confirmation_for_delete_' . $type);
    }

    /**
     * Delete Tax Class
     *
     * @param string $optionLabel
     * @param string $multiselect
     * @param string $msg
     */
    public function deleteTaxClass($optionLabel, $multiselect, $msg)
    {
        //delete tax class
        $this->clickButton('add_rule');
        $this->clickControl('link', 'tax_rule_info_additional_link');
        $containerXpath = $this->_getControlXpath('composite_multiselect', $multiselect);
        $labelLocator = "//div[normalize-space(label/span)='$optionLabel']";
        $generalElement = $this->getElement($containerXpath);
        $optionElement = $this->getChildElement($generalElement, $labelLocator);
        $optionElement->click();
        $deleteButton = $this->getChildElement($optionElement, "//span[@title='Delete']");
        $this->moveto($deleteButton);
        $deleteButton->click();
        //First message
        $this->waitUntil(function ($testCase) {
            /** @var Mage_Selenium_TestCase $testCase */
            $testCase->alertText();
            return true;
        }, 5);
        $alertText = $this->alertText();
        $this->acceptAlert();
        $this->assertSame($this->_getMessageXpath('confirmation_for_delete_class'), $alertText,
            'Confirmation message is incorrect');
        //Second message
        $this->waitUntil(function ($testCase) {
            /** @var Mage_Selenium_TestCase $testCase */
            $testCase->alertText();
            return true;
        }, 5);
        $alertText = $this->alertText();
        $this->acceptAlert();
        $this->assertSame($this->_getMessageXpath($msg), $alertText, 'Confirmation message is incorrect');
    }

    /**
     * Delete all Tax Rules except specified in $excludeList
     *
     * @param array $excludeList
     */
    public function deleteRulesExceptSpecified(array $excludeList = array())
    {
        $rules = array();
        $columnId = $this->getColumnIdByName('Name');
        $elements = $this->getControlElements('pageelement', 'rule_line', null, false);
        /** @var PHPUnit_Extensions_Selenium2TestCase_Element $element */
        foreach ($elements as $element) {
            $name = trim($this->getChildElement($element, "td[$columnId]")->text());
            if (!in_array($name, $excludeList)) {
                $rules[] = $name;
            }
        }
        foreach ($rules as $rule) {
            $this->deleteTaxItem(array('filter_name' => $rule), 'rule');
        }
    }
}