<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CurrencySymbol\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\CurrencySymbol\Test\Fixture\CurrencySymbolEntity;
use Magento\CurrencySymbol\Test\Page\Adminhtml\SystemCurrencySymbolIndex;

/**
 * Test Creation for CreateCustomVariableEntity
 *
 * Test Flow:
 * Preconditions:
 * 1. Create simple product
 *
 * Steps:
 * 1. Login to backend.
 * 2. Navigate to Stores->Currency Symbols
 * 3. Make changes according to dataset.
 * 4. Click 'Save Currency Symbols' button
 * 5. Perform all asserts.
 *
 * @group Currency_(PS)
 * @ZephyrId MAGETWO-26600
 */
class EditCurrencySymbolEntityTest extends Injectable
{
    /**
     * System Currency Symbol grid page
     *
     * @var SystemCurrencySymbolIndex
     */
    protected $currencySymbolIndex;

    /**
     * CurrencySymbolEntity fixture
     *
     * @var CurrencySymbolEntity
     */
    protected $currencySymbolDefault;

    /**
     * Injection data
     *
     * @param SystemCurrencySymbolIndex $currencySymbolIndex
     * @param CurrencySymbolEntity $currencySymbolDefault
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __inject(
        SystemCurrencySymbolIndex $currencySymbolIndex,
        CurrencySymbolEntity $currencySymbolDefault,
        FixtureFactory $fixtureFactory
    ) {
        $this->currencySymbolIndex = $currencySymbolIndex;
        $this->currencySymbolDefault = $currencySymbolDefault;

        /**@var CatalogProductSimple $catalogProductSimple */
        $product = $fixtureFactory->createByCode(
            'catalogProductSimple',
            ['dataSet' => 'product_with_category']
        );
        $product->persist();

        return ['product' => $product];
    }

    /**
     * Edit Currency Symbol Entity test
     *
     * @param CurrencySymbolEntity $currencySymbol
     * @return void
     */
    public function test(CurrencySymbolEntity $currencySymbol)
    {
        // Steps
        $this->currencySymbolIndex->open();
        $this->currencySymbolIndex->getCurrencySymbolForm()->fill($currencySymbol);
        $this->currencySymbolIndex->getFormPageActions()->save();
    }

    /**
     * Use Standard Currency Symbol
     *
     * @return void
     */
    public function tearDown()
    {
        $this->currencySymbolIndex->open();
        $this->currencySymbolIndex->getCurrencySymbolForm()->fill($this->currencySymbolDefault);
        $this->currencySymbolIndex->getFormPageActions()->save();
    }
}
