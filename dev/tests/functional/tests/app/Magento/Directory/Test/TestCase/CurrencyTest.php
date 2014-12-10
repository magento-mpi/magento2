<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Directory\Test\TestCase;

use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;

/**
 * Currency
 *
 */
class CurrencyTest extends Functional
{
    /**
     * Switching display currency on the store front
     *
     * @ZephyrId MAGETWO-12427
     */
    public function testSwitchDisplayCurrency()
    {
        $product = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct();
        $product->switchData('simple');
        $product->persist();

        $currency = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $currency->switchData('allowed_currencies');
        $currency->persist();

        $currencyRate = Factory::getFixtureFactory()->getMagentoDirectoryCurrency();
        $currencyRate->switchData('usd_eur_rates');
        $currencyRate->persist();

        $frontendHomePage = Factory::getPageFactory()->getCmsIndexIndex();
        $categoryPage = Factory::getPageFactory()->getCatalogCategoryView();

        $frontendHomePage->open();
        $frontendHomePage->getTopmenu()->selectCategoryByName($product->getCategoryName());

        $productListBlock = $categoryPage->getListProductBlock();
        $this->assertEquals('$10.00', $productListBlock->getPrice($product->getProductId()));

        $browser = Factory::getClientBrowser();
        $currencySwitcherBlock = Factory::getBlockFactory()->getMagentoDirectoryCurrencySwitcher(
            $browser->find('.switcher.currency', Locator::SELECTOR_CSS)
        );

        $objectManager = Factory::getObjectManager();
        $currencyFixture = $objectManager->create(
            '\Magento\CurrencySymbol\Test\Fixture\CurrencySymbolEntity',
            ['dataSet' => 'currency_symbols_eur']
        );
        $currencySwitcherBlock->switchCurrency($currencyFixture);

        $categoryPage = Factory::getPageFactory()->getCatalogCategoryView();
        $productListBlock = $categoryPage->getListProductBlock();
        $this->assertEquals('€8.00', $productListBlock->getPrice($product->getProductId()));
    }
}
