<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Test\TestCase;

use Magento\Customer\Test\Fixture\AddressInjectable;
use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Tax\Test\Fixture\TaxRule;
use Magento\Tax\Test\Page\Adminhtml\TaxRuleNew;
use Magento\Tax\Test\Page\Adminhtml\TaxRuleIndex;

/**
 * Test Creation for Update TaxRuleEntity
 *
 * Test Flow:
 * Preconditions:
 * 1. 1 simple product is created.
 * 2. Tax Rule is created.
 *
 * Steps:
 * 1. Login to backend
 * 2. Navigate to Stores > Tax Rules
 * 3. Click Tax Rule from grid
 * 4. Edit test value(s) according to dataset.
 * 5. Click 'Save' button.
 * 6. Perform all asserts.
 *
 * @group Tax_(CS)
 * @ZephyrId MAGETWO-20996
 */
class UpdateTaxRuleEntityTest extends Injectable
{
    /**
     * Tax Rule grid page
     *
     * @var TaxRuleIndex
     */
    protected $taxRuleIndexPage;

    /**
     * Tax Rule new and edit page
     *
     * @var TaxRuleNew
     */
    protected $taxRuleNewPage;

    /**
     * Prepare data
     *
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $productSimple = $fixtureFactory->createByCode('catalogProductSimple', ['dataSet' => '100_dollar_product']);
        $productSimple->persist();
        $customer = $fixtureFactory->createByCode('customerInjectable', ['dataSet' => 'default']);
        $customer->persist();

        return [
            'productSimple' => $productSimple,
            'customer' => $customer,
        ];
    }

    /**
     * Injection data
     *
     * @param TaxRuleIndex $taxRuleIndexPage
     * @param TaxRuleNew $taxRuleNewPage
     * @return void
     */
    public function __inject(
        TaxRuleIndex $taxRuleIndexPage,
        TaxRuleNew $taxRuleNewPage
    ) {
        $this->taxRuleIndexPage = $taxRuleIndexPage;
        $this->taxRuleNewPage = $taxRuleNewPage;
    }

    /**
     * Update Tax Rule Entity test
     *
     * @param TaxRule $initialTaxRule
     * @param TaxRule $taxRule
     * @param AddressInjectable $address
     * @param array $shipping
     * @return void
     */
    public function testUpdateTaxRule(
        TaxRule $initialTaxRule,
        TaxRule $taxRule,
        AddressInjectable $address,
        array $shipping
    ) {
        // Precondition
        $initialTaxRule->persist();

        // Steps
        $filters = [
            'code' => $initialTaxRule->getCode(),
        ];
        $this->taxRuleIndexPage->open();
        $this->taxRuleIndexPage->getTaxRuleGrid()->searchAndOpen($filters);
        $this->taxRuleNewPage->getTaxRuleForm()->fill($taxRule);
        $this->taxRuleNewPage->getFormPageActions()->save();
    }
}
