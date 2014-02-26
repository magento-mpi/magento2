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

use Mtf\Fixture\FixtureInterface;
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
     * 'Save and Continue Edit' button
     *
     * @var string
     */
    protected $saveAndContinue = '#save_and_continue';

    /**
     * Tax rate block
     *
     * @var string
     */
    protected $taxRateBlock = '[class*=tax_rate]';

    /**
     * Get tax rate block
     *
     * @return \Magento\Tax\Test\Block\Adminhtml\Rule\Edit\TaxRate
     */
    protected function getTaxRateBlock()
    {
        return Factory::getBlockFactory()->getMagentoTaxAdminhtmlRuleEditTaxRate(
            $this->_rootElement->find($this->taxRateBlock, Locator::SELECTOR_CSS)
        );
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
        $this->_rootElement->find($this->name, Locator::SELECTOR_CSS)->setValue($fixture->getTaxRuleName());
        $this->getTaxRateBlock()->selectTaxRate($fixture->getTaxRate());
        $this->_rootElement->find($this->additionalSettings, Locator::SELECTOR_CSS)->click();
        $this->getTaxClassBlock('customer')->selectTaxClass($fixture->getTaxClass('customer'));
        $this->getTaxClassBlock('product')->selectTaxClass($fixture->getTaxClass('product'));
        if (!empty($data['priority'])) {
            $this->_rootElement->find($this->priority, Locator::SELECTOR_CSS)->setValue($fixture->getTaxRulePriority());
        }
        if (!empty($data['position'])) {
            $this->_rootElement->find($this->position, Locator::SELECTOR_CSS)->setValue($fixture->getTaxRulePosition());
        }
    }

    /**
     * Click Save And Continue Button on Form
     */
    public function clickSaveAndContinue()
    {
        $this->_rootElement->find($this->saveAndContinue, Locator::SELECTOR_CSS)->click();
    }
}
