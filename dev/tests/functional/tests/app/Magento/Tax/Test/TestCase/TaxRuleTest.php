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

namespace Magento\Tax\Test\TestCase;

use Magento\Tax\Test\Page\Adminhtml\TaxRuleIndex;
use Magento\Tax\Test\Page\Adminhtml\TaxRuleNew;
use Mtf\Fixture\FixtureFactory;
use Magento\Tax\Test\Fixture\TaxRule;
use Mtf\TestCase\Injectable;

/**
 * Class TaxRuleTest
 * Functional test for Tax Rule configuration
 *
 * @package Magento\Tax\Test\TestCase
 */
class TaxRuleTest extends Injectable
{
    /**
     * @var TaxRuleIndex
     */
    protected $taxRuleIndexPage;

    /**
     * @var TaxRuleNew
     */
    protected $taxRuleNewPage;

    /**
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $taxRule = $fixtureFactory->createByCode('taxRule', ['dataSet' => 'default']);

        return [
            'taxRule' => $taxRule,
        ];
    }

    /**
     * @param TaxRuleIndex $taxRuleIndexPage
     * @param TaxRuleNew $taxRuleNewPage
     */
    public function __inject(
        TaxRuleIndex $taxRuleIndexPage,
        TaxRuleNew $taxRuleNewPage
    ) {
        $this->taxRuleIndexPage = $taxRuleIndexPage;
        $this->taxRuleNewPage = $taxRuleNewPage;
    }

    /**
     * Create Tax Rule with new and existing Tax Rate, Customer Tax Class, Product Tax Class
     *
     * @ZephyrId MAGETWO-12438
     *
     * @param TaxRule $taxRule
     */
    public function testCreateTaxRule(TaxRule $taxRule)
    {
        //Steps
        $this->taxRuleIndexPage->open();
        $this->taxRuleIndexPage->getGridPageActions()->addNew();
        $this->taxRuleNewPage->getTaxRuleForm()->fill($taxRule);
        $this->taxRuleNewPage->getFormPageActions()->saveAndContinue();
        //Verifying
        $this->taxRuleNewPage->getMessageBlock()->assertSuccessMessage();
        $this->_assertOnGrid($taxRule);
    }

    /**
     * Assert existing tax rule on manage tax rule grid
     *
     * @param TaxRule $taxRule
     */
    protected function _assertOnGrid(TaxRule $taxRule)
    {
        //Data
        $filter = [
            'code' => $taxRule->getCode(),
            'tax_rate' => implode(', ', $taxRule->getTaxRate())
        ];
        if ($taxRule->getTaxCustomerClass() !== null) {
            $filter['customer_tax_class'] = implode(', ', $taxRule->getTaxCustomerClass());
        }
        if ($taxRule->getTaxProductClass() !== null) {
            $filter['product_tax_class'] = implode(', ', $taxRule->getTaxProductClass());
        }

        //Verification
        $this->taxRuleIndexPage->open();
        $this->assertTrue(
            $this->taxRuleIndexPage->getTaxRuleGrid()->isRowVisible($filter),
            'New tax rule was not found.'
        );
    }
}
