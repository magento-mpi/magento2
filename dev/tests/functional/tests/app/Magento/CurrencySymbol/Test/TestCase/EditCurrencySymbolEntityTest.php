<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CurrencySymbol\Test\TestCase;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\CurrencySymbol\Test\Fixture\CurrencySymbolEntity;
use Magento\CurrencySymbol\Test\Page\Adminhtml\SystemCurrencyIndex;
use Magento\CurrencySymbol\Test\Page\Adminhtml\SystemCurrencySymbolIndex;
use Mtf\Fixture\FixtureFactory;
use Mtf\ObjectManager;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for EditCurrencySymbolEntity
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
        $this->currencySymbolIndex->getPageActions()->save();
    }

    /**
     * Disabling currency which has been added.
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
