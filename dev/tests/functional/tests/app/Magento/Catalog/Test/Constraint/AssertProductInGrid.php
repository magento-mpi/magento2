<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
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
     * @param CatalogProductSimple $product
     * @param CatalogProductIndex $productPageGrid
     * @return void
     */
    public function processAssert(CatalogProductSimple $product, CatalogProductIndex $productPageGrid)
    {
        $filter = ['sku' => $product->getSku()];
        $productPageGrid->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $productPageGrid->getProductGrid()->isRowVisible($filter),
            'Product with sku \'' . $product->getSku() . '\' is absent in Products grid.'
        );
    }

    /**
     * @inheritdoc
     */
    public function toString()
    {
        return 'Product is present in Products grid.';
    }
}
