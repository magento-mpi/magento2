<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CurrencySymbol\Test\TestCase;

use Mtf\ObjectManager;
use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\CurrencySymbol\Test\Fixture\CurrencySymbolEntity;
use Magento\CurrencySymbol\Test\Page\Adminhtml\SystemCurrencyIndex;
use Magento\CurrencySymbol\Test\Page\Adminhtml\SystemCurrencySymbolIndex;

/**
 * Test Creation for Reset CurrencySymbolEntity
 *
 * Test Flow:
 * Preconditions:
 * 1. Create simple product
 * 2. Create custom Currency Symbol
 *
 * Steps:
 * 1. Login to backend.
 * 2. Navigate to Stores->Currency Symbols
 * 3. Make changes according to dataset.
 * 4. Click 'Save Currency Symbols' button
 * 5. Perform all asserts.
 *
 * @group Currency_(PS)
 * @ZephyrId MAGETWO-26638
 */
class ResetCurrencySymbolEntityTest extends Injectable
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
     * Prepare data
     *
     * @param FixtureFactory $fixtureFactory
     * @param SystemCurrencyIndex $currencyIndex
     * @return void
     */
    public function __prepare(FixtureFactory $fixtureFactory, SystemCurrencyIndex $currencyIndex)
    {
        $config = $fixtureFactory->createByCode('configData', ['dataSet' => 'config_currency_symbols_usd_and_uah']);
        $config->persist();

        $currencyIndex->open();
        $currencyIndex->getGridPageActions()->clickImportButton();
        $currencyIndex->getMainPageActions()->saveCurrentRate();
    }

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

        $currencySymbolOriginal = $fixtureFactory->createByCode(
            'currencySymbolEntity',
            ['dataSet' => 'custom']
        );
        $currencySymbolOriginal->persist();


        return ['product' => $product, 'currencySymbolOriginal' => $currencySymbolOriginal];
    }

    /**
     * Edit Currency Symbol Entity test
     *
     * @param CurrencySymbolEntity $currencySymbol
     * @param string $currencySymbolDefault
     * @return void
     */
    public function test(CurrencySymbolEntity $currencySymbol, $currencySymbolDefault)
    {
        // Steps
        $this->currencySymbolIndex->open();
        $this->currencySymbolIndex->getCurrencySymbolForm()->fill($currencySymbol);
        $this->currencySymbolIndex->getPageActions()->save();
    }

    /**
     * Use Standard Currency Symbol
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        $fixtureFactory = ObjectManager::getInstance()->create('Mtf\Fixture\FixtureFactory');
        $config = $fixtureFactory->createByCode('configData', ['dataSet' => 'config_currency_symbols_usd']);
        $config->persist();
    }
}
