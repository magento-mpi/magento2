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
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;

/**
 * Class AssertRelatedProductsSection
 * Assert that product is displayed in related products section
 */
class AssertRelatedProductsSection extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'middle';

    /**
     * Assert that product is displayed in related products section
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
            \PHPUnit_Framework_Assert::assertTrue(
                $catalogProductView->getRelatedProductBlock()->isRelatedProductVisible($sellingProduct->getName()),
                'Product \'' . $sellingProduct->getName() . '\' is absent in related products.'
            );
        }
    }

    /**
     * Text success product is displayed in related products section
     *
     * @return string
     */
    public function toString()
    {
        return 'Product is displayed in related products section.';
    }
}
