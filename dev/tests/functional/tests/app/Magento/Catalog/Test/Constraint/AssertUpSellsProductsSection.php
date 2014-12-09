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
use Mtf\Fixture\InjectableFixture;
use Mtf\Fixture\FixtureInterface;

/**
 * Class AssertUpSellsProductsSection
 * Assert that product is displayed in up-sell section
 */
class AssertUpSellsProductsSection extends AbstractConstraint
{
    /* tags */
     const SEVERITY = 'middle';
     /* end tags */

    /**
     * Assert that product is displayed in up-sell section
     *
     * @param Browser $browser
     * @param FixtureInterface $product
     * @param InjectableFixture[] $relatedProducts,
     * @param CatalogProductView $catalogProductView
     * @return void
     */
    public function processAssert(
        Browser $browser,
        FixtureInterface $product,
        array $relatedProducts,
        CatalogProductView $catalogProductView
    ) {
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        foreach ($relatedProducts as $relatedProduct) {
            \PHPUnit_Framework_Assert::assertTrue(
                $catalogProductView->getUpsellBlock()->isUpsellProductVisible($relatedProduct->getName()),
                'Product \'' . $relatedProduct->getName() . '\' is absent in up-sells products.'
            );
        }
    }

    /**
     * Text success product is displayed in up-sell section
     *
     * @return string
     */
    public function toString()
    {
        return 'Product is displayed in up-sell section.';
    }
}
