<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Client\Browser;
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class AssertNoUpSellsProductsSection
 * Assert that product is not displayed in up-sell section
 */
class AssertNoUpSellsProductsSection extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'middle';

    /**
     * Assert that product is not displayed in up-sell section
     *
     * @param Browser $browser
     * @param CatalogProductSimple $product
     * @param array $sellingProducts
     * @param CatalogProductView $catalogProductView
     * @return void
     */
    public function processAssert(
        Browser $browser,
        CatalogProductSimple $product,
        array $sellingProducts,
        CatalogProductView $catalogProductView
    ) {
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        foreach ($sellingProducts as $sellingProduct) {
            \PHPUnit_Framework_Assert::assertFalse(
                $catalogProductView->getUpsellBlock()->isUpsellProductVisible($sellingProduct->getName()),
                'Product \'' . $sellingProduct->getName() . '\' is exist in up-sells products.'
            );
        }
    }

    /**
     * Text success product is not displayed in up-sell section
     *
     * @return string
     */
    public function toString()
    {
        return 'Product is not displayed in up-sell section.';
    }
}
