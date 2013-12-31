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

namespace Magento\Directory\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Mtf\Client\Element\Locator;

/**
 * Currency
 *
 * @package Magento\Directory\Test\TestCase
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
        $currencySwitcherBlock->switchCurrency('EUR');

        $categoryPage = Factory::getPageFactory()->getCatalogCategoryView();
        $productListBlock = $categoryPage->getListProductBlock();
        $this->assertEquals('€8.00', $productListBlock->getPrice($product->getProductId()));
    }
}
