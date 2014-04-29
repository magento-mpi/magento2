<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Mtf\Fixture\InjectableFixture;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;

/**
 * Class AssertProductInGrid
 *
 * @package Magento\Catalog\Test\Constraint
 */
class AssertProductInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert product availability in Products Grid
     *
     * @param InjectableFixture $product
     * @param CatalogProductIndex $productPageGrid
     * @return void
     */
    public function processAssert(InjectableFixture $product, CatalogProductIndex $productPageGrid)
    {
        $filter = ['sku' => $product->getData('sku')];
        $productPageGrid->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $productPageGrid->getProductGrid()->isRowVisible($filter),
            'Product with sku \'' . $product->getData('sku') . '\' is absent in Products grid.'
        );
    }

    /**
     * @return string
     */
    public function toString()
    {
        return 'Product is present in Products grid.';
    }
}
