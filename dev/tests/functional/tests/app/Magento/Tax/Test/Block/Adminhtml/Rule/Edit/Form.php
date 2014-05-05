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

namespace Magento\Tax\Test\Block\Adminhtml\Rule\Edit;

use Magento\Tax\Test\Fixture\TaxRule;
use Mtf\Block\BlockFactory;
use Mtf\Block\Form as FormInterface;
use Mtf\Block\Mapper;
use Mtf\Client\Browser;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Mtf\Fixture\FixtureInterface;

/**
 * Class Form
 * Form for tax rule creation
 *
 * @package Magento\Tax\Test\Block\Adminhtml\Rule\Edit
 */
class Form extends FormInterface
{
    /**
     * @var BlockFactory
     */
    protected $blockFactory;

    /**
     * @var Browser
     */
    protected $browser;

    /**
     * Tax rule name
     *
     * @var string
     */
    protected $name = '#code';

    /**
     * Tax rule priority field
     *
     * @var string
     */
    protected $priority = '#priority';

    /**
     * Tax rule sort order field
     *
     * @var string
     */
    protected $position = '#position';

    /**
     * 'Additional Settings' link
     *
     * @var string
     */
    protected $additionalSettings = '#details-summarybase_fieldset';

    /**
     * Tax rate block
     *
     * @var string
     */
    protected $taxRateBlock = '[class*=tax_rate]';

    /**
     * Tax rate form
     *
     * @var string
     */
    protected $taxRateForm = '//*[contains(@class, "tax-rate-popup")]';

    /**
     * Customer Tax Class block
     *
     * @var string
     */
    protected $taxCustomerBlock = '[class*=tax_customer_class]';

    /**
     * Product Tax Class block
     *
     * @var string
     */
    protected $taxProductBlock = '[class*=tax_product_class]';

    /**
     * XPath selector for finding needed option by its value
     *
     * @var string
     */
    protected $optionMaskElement = './/*[contains(@class, "mselect-list-item")]//label/span[text()="%s"]';

    /**
     * Css selector for Add New button
     *
     * @var string
     */
    protected $addNewButton = '.mselect-button-add';

    /**
     * Css selector for Add New tax class input
     *
     * @var string
     */
    protected $addNewInput = '.mselect-input';

    /**
     * Css selector for Add New save button
     *
     * @var string
     */
    protected $saveButton = '.mselect-save';

    /**
     * @constructor
     * @param Element $element
     * @param Mapper $mapper
     * @param BlockFactory $blockFactory
     * @param Browser $browser
     */
    public function __construct(Element $element, Mapper $mapper, BlockFactory $blockFactory, Browser $browser)
    {
        $this->mapper = $mapper;
        parent::__construct($element, $mapper);
        $this->blockFactory = $blockFactory;
        $this->browser = $browser;
    }

    /**
     * Fill the root form
     *
     * @param FixtureInterface $fixture
     * @param Element $element
     * @return $this|void
     */
    public function fill(FixtureInterface $fixture, Element $element = null)
    {
        /** @var TaxRule $fixture */
        $this->_rootElement->find($this->name)->setValue($fixture->getCode());
        $taxRateBlock = $this->_rootElement->find($this->taxRateBlock, Locator::SELECTOR_CSS, 'multiselectlist');
        $this->setTaxRates($fixture, $taxRateBlock);

        $this->additionalSettings();
        $taxCustomerBlock = $this->_rootElement->find(
            $this->taxCustomerBlock,
            Locator::SELECTOR_CSS,
            'multiselectlist'
        );
        $this->setTaxClass($fixture->getTaxCustomerClass(), $taxCustomerBlock);
        $taxProductBlock = $this->_rootElement->find($this->taxProductBlock, Locator::SELECTOR_CSS, 'multiselectlist');
        $this->setTaxClass($fixture->getTaxProductClass(), $taxProductBlock);

        if ($fixture->getPriority() !== null) {
            $this->_rootElement->find($this->priority, Locator::SELECTOR_CSS)->setValue($fixture->getPriority());
        }
        if ($fixture->getPosition() !== null) {
            $this->_rootElement->find($this->position, Locator::SELECTOR_CSS)->setValue($fixture->getPosition());
        }
    }

    /**
     * Method to set tax rate
     *
     * @param TaxRule $taxRule
     * @param Element $element
     */
    protected function setTaxRates($taxRule, $element)
    {
        /** @var \Magento\Tax\Test\Block\Adminhtml\Rule\Edit\TaxRate $taxRateForm */
        $taxRateForm = $this->blockFactory->create(
            'Magento\Tax\Test\Block\Adminhtml\Rule\Edit\TaxRate',
            ['element' => $this->browser->find($this->taxRateForm, Locator::SELECTOR_XPATH)]
        );

        /** @var \Magento\Tax\Test\Fixture\TaxRule\TaxRate $taxRatesFixture */
        $taxRatesFixture = $taxRule->getDataFieldConfig('tax_rate')['fixture'];
        $taxRatesFixture = $taxRatesFixture->getTaxRate();
        $taxRatesData = $taxRule->getTaxRate();
        $issetValues = [];
        $absentValues = [];
        foreach ($taxRatesData as $key => $taxRate) {
            $option = $element->find(sprintf($this->optionMaskElement, $taxRate), Locator::SELECTOR_XPATH);
            if ($option->isVisible()) {
                $issetValues[] = $taxRate;
            } else {
                $absentValues[] = $taxRatesFixture[$key];
            }
        }
        $element->setValue($issetValues);

        if (count($absentValues) > 0) {
            foreach ($absentValues as $value) {
                /** @var \Magento\Tax\Test\Fixture\TaxRate $value */
                $element->find($this->addNewButton)->click();
                $taxRateForm->fill($value);
                $taxRateForm->saveTaxRate();
                $code = $value->getCode();
                $this->waitUntilOptionIsVisible($element, $code);
            }
        }
    }

    /**
     * Waiting until option in list is visible
     *
     * @param Element $element
     * @param $value
     */
    protected function waitUntilOptionIsVisible($element, $value)
    {
        $element->waitUntil(
            function () use ($element, $value) {
                $productSavedMessage = $element->find(
                    sprintf($this->optionMaskElement, $value),
                    Locator::SELECTOR_XPATH
                );
                return $productSavedMessage->isVisible() ? true : null;
            }
        );
    }

    /**
     * Click Additional Settings on Form
     */
    public function additionalSettings()
    {
        $this->_rootElement->find($this->additionalSettings)->click();
    }

    /**
     * Method to set tax classes
     *
     * @param array $values
     * @param Element $element
     */
    protected function setTaxClass($values, $element)
    {
        if ($values !== null) {
            $issetValues = [];
            $absentValues = [];
            foreach ($values as $value) {
                $option = $element->find(sprintf($this->optionMaskElement, $value), Locator::SELECTOR_XPATH);
                if ($option->isVisible()) {
                    $issetValues[] = $value;
                } else {
                    $absentValues[] = $value;
                }
            }
            $element->setValue($issetValues);

            if (count($absentValues) > 0) {
                foreach ($absentValues as $value) {
                    $element->find($this->addNewButton)->click();
                    $element->find($this->addNewInput)->setValue($value);
                    $element->find($this->saveButton)->click();
                    $this->waitUntilOptionIsVisible($element, $value);
                }
            }
        }
    }
}
