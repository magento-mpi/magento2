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

use Mtf\Fixture;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Mtf\Block\Form as FormInterface;
use Magento\Tax\Test\Fixture\TaxRule;

/**
 * Class Form
 * Form for tax rule creation
 *
 * @package Magento\Tax\Test\Block\Adminhtml\Rule\Edit
 */
class Form extends FormInterface
{
    /**
     * Tax rule name
     *
     * @var string
     */
    private $name;

    /**
     * Tax rule priority field
     *
     * @var string
     */
    private $priority;

    /**
     * Tax rule sort order field
     *
     * @var string
     */
    private $position;

    /**
     * 'Additional Settings' link
     *
     * @var string
     */
    private $additionalSettings;

    /**
     * 'Save and Continue Edit' button
     *
     * @var string
     */
    private $saveAndContinue;

    /**
     * Tax rate block
     *
     * @var TaxRate
     */
    private $taxRateBlock;

    /**
     * Initialize elements in block
     */
    protected function _init()
    {
        //Elements
        $this->name = 'code';
        $this->priority = 'priority';
        $this->position = 'position';
        $this->additionalSettings = 'details-summarybase_fieldset';
        $this->saveAndContinue = 'save_and_continue';
        //Blocks
        $this->taxRateBlock = Factory::getBlockFactory()->getMagentoTaxAdminhtmlRuleEditTaxRate(
            $this->_rootElement->find('[class*=tax_rate]', Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get tax rate block
     *
     * @return \Magento\Tax\Test\Block\Adminhtml\Rule\Edit\TaxRate
     */
    protected function getTaxRateBlock()
    {
        return $this->taxRateBlock;
    }

    /**
     * Get Customer/Product Tax Classes bloc
     *
     * @param string $taxClass (e.g. customer|product)
     *
     * @return \Magento\Tax\Test\Block\Adminhtml\Rule\Edit\TaxClass
     */
    protected function getTaxClassBlock($taxClass)
    {
        $taxClassBlock = Factory::getBlockFactory()->getMagentoTaxAdminhtmlRuleEditTaxClass(
            $this->_rootElement->find('[class*=tax_' . $taxClass . ']', Locator::SELECTOR_CSS)
        );

        return $taxClassBlock;
    }

    /**
     * Fill Tax Rule data on the form
     *
     * @param TaxRule $fixture
     */
    public function fillTaxRuleData(TaxRule $fixture)
    {
        $data = $fixture->getData('fields');
        $this->_rootElement->find($this->name, Locator::SELECTOR_ID)->setValue($fixture->getTaxRuleName());
        $this->getTaxRateBlock()->selectTaxRate($fixture->getTaxRate());
        $this->_rootElement->find($this->additionalSettings, Locator::SELECTOR_ID)->click();
        $this->getTaxClassBlock('customer')->selectTaxClass($fixture->getTaxClass('customer'));
        $this->getTaxClassBlock('product')->selectTaxClass($fixture->getTaxClass('product'));
        if (!empty($data['priority'])) {
            $this->_rootElement->find($this->priority, Locator::SELECTOR_ID)->setValue($fixture->getTaxRulePriority());
        }
        if (!empty($data['position'])) {
            $this->_rootElement->find($this->position, Locator::SELECTOR_ID)->setValue($fixture->getTaxRulePosition());
        }
    }

    /**
     * Click Save And Continue Button on Form
     */
    public function clickSaveAndContinue()
    {
        $this->_rootElement->find($this->saveAndContinue, Locator::SELECTOR_ID)->click();
    }
}
