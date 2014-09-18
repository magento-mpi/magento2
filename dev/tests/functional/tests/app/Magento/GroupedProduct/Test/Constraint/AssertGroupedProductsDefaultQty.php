<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Test\Constraint;

use Mtf\Client\Browser;
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\GroupedProduct\Test\Fixture\CatalogProductGrouped;

/**
 * Class AssertGroupedProductsDefaultQty
 */
class AssertGroupedProductsDefaultQty extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that default qty for sub products in grouped product displays according to dataset on product page.
     *
     * @param CatalogProductView $groupedProductView
     * @param CatalogProductGrouped $product
     * @param Browser $browser
     * @return void
     */
    public function processAssert(
        CatalogProductView $groupedProductView,
        CatalogProductGrouped $product,
        Browser $browser
    ) {
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $groupedBlock = $groupedProductView->getGroupedViewBlock()->getGroupedProductBlock();
        $groupedProduct = $product->getData();

        foreach ($groupedProduct['associated']['assigned_products'] as $item) {
            \PHPUnit_Framework_Assert::assertEquals(
                $groupedBlock->getQty($item['id']),
                $item['qty'],
                'Default qty for sub product "' . $item['name'] . '" in grouped product according to dataset.'
            );
        }
    }

    /**
     * Text of Visible in grouped assert for default qty for sub products
     *
     * @return string
     */
    public function toString()
    {
        return 'Default qty for sub products in grouped product displays according to dataset on product page.';
    }
}
