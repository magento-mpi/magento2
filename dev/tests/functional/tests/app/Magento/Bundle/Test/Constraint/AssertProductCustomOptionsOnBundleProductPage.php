<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Constraint;

use Mtf\Client\Browser;
use Mtf\Fixture\FixtureInterface;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Catalog\Test\Constraint\AssertProductCustomOptionsOnProductPage;

/**
 * Class AssertProductCustomOptionsOnBundleProductPage
 * Assertion that commodity options are displayed correctly on bundle product page
 */
class AssertProductCustomOptionsOnBundleProductPage extends AssertProductCustomOptionsOnProductPage
{
    /**
     * Flag for verify price data
     *
     * @var bool
     */
    protected $isPrice = false;

    /**
     * Open product view page
     *
     * @param CatalogProductView $catalogProductView
     * @param FixtureInterface $product
     * @param Browser $browser
     * @return void
     */
    protected function openProductPage(
        CatalogProductView $catalogProductView,
        FixtureInterface $product,
        Browser $browser
    ) {
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $catalogProductView->getViewBlock()->clickCustomize();
    }
}
