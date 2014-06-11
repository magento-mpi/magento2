<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Test\TestCase;

use Magento\Tax\Test\Fixture\TaxRate;
use Magento\Tax\Test\Page\Adminhtml\TaxRateIndex;
use Magento\Tax\Test\Page\Adminhtml\TaxRateNew;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Injectable;

/**
 * Test creation for delete TaxRateEntity
 *
 * Test Flow:
 * Preconditions:
 * 1. Create Tax Rate
 * 2. Create Tax Rule
 *
 * Steps:
 * 1. Log in as default admin user
 * 2. Go to Stores -> Taxes -> Tax Zones and Rates
 * 3. Open created tax rate
 * 4. Click Delete Rate
 * 5. Perform all assertions
 *
 * @group Tax_(CS)
 * @ZephyrId MAGETWO-23295
 */
class DeleteTaxRateEntityTest extends Injectable
{
    /**
     * Tax Rate grid page
     *
     * @var TaxRateIndex
     */
    protected $taxRateIndex;

    /**
     * Tax Rate new/edit page
     *
     * @var TaxRateNew
     */
    protected $taxRateNew;

    /**
     * Prepare data
     *
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $taxRule = $fixtureFactory->createByCode('taxRule', ['dataSet' => 'default']);
        $taxRule->persist();

        return ['taxRule' => $taxRule];
    }

    /**
     * Injection data
     *
     * @param TaxRateIndex $taxRateIndex
     * @param TaxRateNew $taxRateNew
     * @return void
     */
    public function __inject(
        TaxRateIndex $taxRateIndex,
        TaxRateNew $taxRateNew
    ) {
        $this->taxRateIndex = $taxRateIndex;
        $this->taxRateNew = $taxRateNew;
    }

    /**
     * Delete Tax Rule Entity test
     *
     * @param TaxRate $taxRate
     * @return void
     */
    public function testDeleteTaxRate(TaxRate $taxRate)
    {
        // Precondition
        $taxRate->persist();

        // Steps
        $filter = [
            'code' => $taxRate->getCode(),
        ];
        $this->taxRateIndex->open();
        $this->taxRateIndex->getTaxRateGrid()->searchAndOpen($filter);
        $this->taxRateNew->getFormPageActions()->delete();
    }
}
