<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Block\Tax\Rule;

use Mtf\Block\Form;
use Mtf\Fixture;
use Mtf\Client\Element\Locator;

/**
 * Class Tax Rule Form
 * Form for creation of the customer
 *
 * @package Magento\Customer\Test\Block\Backend
 */
class Edit extends Form
{
/** !#+ Fields contain XPath/CSS/... for elements on page  */
    protected $_taxRuleName;
    protected $_additionalSettings;
    protected $_addNewTaxRateButton;
    protected $_taxRateUiDialog;
    protected $_saveButtonUiDialog;
    protected $_checkedTaxRateOption;
    protected $_taxRateOption;
    protected $_addNewCustomerTaxClassButton;
    protected $_newCustomerTaxClassInput;
    protected $_newCustomerTaxClassApplyButton;
    protected $_customerTaxClassCheckedOption;
    protected $_customerTaxClassOption;
    protected $_addNewProductTaxClassButton;
    protected $_newProductTaxClassInput;
    protected $_newProductTaxClassApplyButton;
    protected $_productTaxClassCheckedOption;
    protected $_productTaxClassOption;
    protected $_priority;
    protected $_sortOrder;
    /** +! */

    /**
     * Initialize elements in block
     */
    protected function _init()
    {
        $this->_taxRuleName = 'code';
        $this->_additionalSettings = 'details-summarybase_fieldset';
        $this->_addNewTaxRateButton = '.field-tax_rate .action-add.mselect-button-add';
        $this->_taxRateUiDialog = '//*[contains(@class, ui-dialog)]//*[@id="tax-rate-form"]/..';
        $this->_saveButtonUiDialog = 'tax-rule-edit-apply-button';
        $this->_checkedTaxRateOption = '//*[contains(@class, "field-tax_rate")]//label[' .
            'input[contains(@class, "checked")]]/span[text()="';
        $this->_taxRateOption = '//*[contains(@class, "field-tax_rate")]//label/span[text()="';
        $this->_addNewCustomerTaxClassButton = '//*[contains(@class, "field-tax_customer_class")]' .
            '/div[@class="control"]//footer/span';
        $this->_addNewProductTaxClassButton = '//*[contains(@class, "field-tax_product_class")]' .
            '/div[@class="control"]//footer/span';
        $this->_newCustomerTaxClassInput = '//*[contains(@class, "field-tax_customer_class")]' .
            '/div[@class="control"]//input[@class="mselect-input"]';
        $this->_newProductTaxClassInput = '//*[contains(@class, "field-tax_product_class")]' .
            '/div[@class="control"]//input[@class="mselect-input"]';
        $this->_newCustomerTaxClassApplyButton = '//*[contains(@class, "field-tax_customer_class")]' .
            '/div[@class="control"]//span[@class="mselect-save"]';
        $this->_newProductTaxClassApplyButton = '//*[contains(@class, "field-tax_product_class")]' .
            '/div[@class="control"]//span[@class="mselect-save"]';
        $this->_customerTaxClassCheckedOption = '//*[contains(@class, "field-tax_customer_class")]//' .
            'label[input[contains(@class, "checked")]]/span[text()="';
        $this->_productTaxClassCheckedOption = '//*[contains(@class, "field-tax_product_class")]//' .
            'label[input[contains(@class, "checked")]]/span[text()="';
        $this->_customerTaxClassOption = '//*[contains(@class, "field-tax_customer_class")]//label/span[text()="';
        $this->_productTaxClassOption = '//*[contains(@class, "field-tax_product_class")]//label/span[text()="';
        $this->_priority = 'priority';
        $this->_sortOrder = 'position';
    }

    /**
     * Select Tax Rate in multiselect and create new one
     *
     * @param array $rate
     */
    protected function _selectTaxRate(array $rate)
    {
        foreach($rate as $key) {
            if(isset($key['rate'])) {
                $this->_rootElement->find($this->_addNewTaxRateButton, Locator::SELECTOR_CSS)
                    ->click();
                $data = $this->dataMapping($key);
                $taxRateDialog = $this->_rootElement
                    ->find($this->_taxRateUiDialog, Locator::SELECTOR_XPATH);
                $this->_fill($data, $taxRateDialog);
                $taxRateDialog->find($this->_saveButtonUiDialog, Locator::SELECTOR_ID)->click();
                $this->waitForElementVisible($this->_checkedTaxRateOption . $key['code']['value'] . '"]',
                    Locator::SELECTOR_XPATH);
            } else {
                $this->_rootElement->find('' . $this->_taxRateOption . $key['code']['value'] . '"]',
                    Locator::SELECTOR_XPATH)->click();
            }
        }

    }

    /**
     * Uncheck all Customer Tax Classes and check some class, add new one
     *
     * @param \Mtf\Fixture $fixture
     */
    protected function _selectCustomerTaxClass(Fixture $fixture)
    {
        $class = $fixture->getTaxCustomerClass();
        if (!is_array($class)) {
            $class = array($class);
        }

        $checkedTaxClass = $this->_rootElement->find('.field-tax_customer_class input', Locator::SELECTOR_CSS);
        while($checkedTaxClass->isSelected())
        {
            $checkedTaxClass->click();
            $checkedTaxClass = $this->_rootElement->find('.field-tax_customer_class input', Locator::SELECTOR_CSS);
        }

        foreach ($class as $value) {
            $taxOption = $this->_rootElement->find($this->_customerTaxClassOption .
                $value . '"]', Locator::SELECTOR_XPATH);
            if (!$taxOption->isVisible()) {
                $this->_rootElement->find($this->_addNewCustomerTaxClassButton, Locator::SELECTOR_XPATH)->click();
                $taxInput = $this->_rootElement->find($this->_newCustomerTaxClassInput, Locator::SELECTOR_XPATH);
                $taxInput->setValue($value);
                $taxInput->find($this->_newCustomerTaxClassApplyButton, Locator::SELECTOR_XPATH)->click();
                $this->waitForElementVisible($this->_customerTaxClassCheckedOption .
                    $value . '"]', Locator::SELECTOR_XPATH);
            } else {
                $this->_rootElement->find($this->_customerTaxClassOption .
                    $value . '"]', Locator::SELECTOR_XPATH)->click();
            }
        }
    }

    /**
     * Uncheck all Product Tax Classes and check some class, add new one
     *
     * @param \Mtf\Fixture $fixture
     */
    protected function _selectProductTaxClass(Fixture $fixture)
    {
        $class = $fixture->getTaxProductClass();
        if (!is_array($class)) {
            $class = array($class);
        }
        //Uncheck checked
        $checkedTaxClass = $this->_rootElement->find('.field-tax_product_class input', Locator::SELECTOR_CSS);
        while ($checkedTaxClass->isSelected()) {
            $checkedTaxClass->click();
            $checkedTaxClass = $this->_rootElement->find('.field-tax_product_class input', Locator::SELECTOR_CSS);
        }

        foreach ($class as $value) {
            $taxOption = $this->_rootElement->find($this->_productTaxClassOption .
                $value . '"]', Locator::SELECTOR_XPATH);
            if (!$taxOption->isVisible()) {
                $this->_rootElement->find($this->_addNewProductTaxClassButton, Locator::SELECTOR_XPATH)->click();
                $taxInput = $this->_rootElement->find($this->_newProductTaxClassInput, Locator::SELECTOR_XPATH);
                $taxInput->setValue($value);
                $taxInput->find($this->_newProductTaxClassApplyButton, Locator::SELECTOR_XPATH)->click();
                $this->waitForElementVisible($this->_productTaxClassCheckedOption .
                    $value . '"]', Locator::SELECTOR_XPATH);
            } else {
                $this->_rootElement->find($this->_productTaxClassOption .
                    $value . '"]', Locator::SELECTOR_XPATH)->click();
            }
        }
    }

    /**
     * Perform all actions on the page:
     * 1. Input Name
     * 2. Select and add new Tax Rate
     * 3. Create and add Customer Tax Class
     * 4. Create and add Product Tax Class
     * 5. Input Priority and Sort Order
     *
     * @param \Mtf\Fixture $fixture
     */
    public function createTaxRule(Fixture $fixture)
    {
        $data = $fixture->getData('fields');
        $this->_rootElement->find($this->_taxRuleName, Locator::SELECTOR_ID)->setValue($fixture->getTaxRuleName());
        $this->_selectTaxRate($data['tax_rate']);
        $this->_rootElement->find($this->_additionalSettings, Locator::SELECTOR_ID)->click();
        $this->_selectCustomerTaxClass($fixture);
        $this->_selectProductTaxClass($fixture);
        $this->_rootElement->find($this->_priority, Locator::SELECTOR_ID)->setValue($data['priority']['value']);
        $this->_rootElement->find($this->_sortOrder, Locator::SELECTOR_ID)->setValue($data['position']['value']);
        $this->clickSaveAndContinue();
    }

    /**
     * Click Save And Continue Button on Form
     */
    public function clickSaveAndContinue()
    {
        $this->_rootElement->find('#save_and_continue')->click();
    }


}
