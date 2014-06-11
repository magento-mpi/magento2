<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Mtf\Fixture\InjectableFixture;
use Magento\GroupedProduct\Test\Page\Product\CatalogProductView;
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
     * @return void
     */
    public function processAssert(CatalogProductView $groupedProductView, CatalogProductGrouped $product)
    {
        $groupedProductView->init($product);
        $groupedProductView->open();
        $groupedBlock = $groupedProductView->getGroupedViewBlock()->getGroupedProductBlock();
        $groupedProduct = $product->getData();

        $preset = $groupedProduct['grouped_products']['preset'];
        $products = $groupedProduct['grouped_products']['products'];
        foreach ($preset['assigned_products'] as $productIncrement => $item) {
            if (!isset($products[$productIncrement])) {
                break;
            }
            /** @var InjectableFixture $fixture */
            $fixture = $products[$productIncrement];
            \PHPUnit_Framework_Assert::assertEquals(
                $groupedBlock->getQty($fixture->getData('id')),
                $item['qty'],
                'Default qty for sub product "' . $fixture->getData('name')
                . '" in grouped product according to dataset.'
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
        return 'That default qty for sub products in grouped product displays accroding to dataset on product page.';
    }
}
