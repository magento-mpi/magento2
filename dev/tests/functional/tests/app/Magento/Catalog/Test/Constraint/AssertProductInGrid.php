<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Fixture\FixtureInterface;
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;

/**
 * Class AssertProductInGrid
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
     * Assert product availability in products grid
     *
     * @param FixtureInterface $product
     * @param CatalogProductIndex $productGrid
     * @return void
     */
    public function processAssert(FixtureInterface $product, CatalogProductIndex $productGrid)
    {
        $filter = ['sku' => $product->getSku()];
        $productGrid->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $productGrid->getProductGrid()->isRowVisible($filter),
            'Product with sku \'' . $product->getSku() . '\' is absent in Products grid.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Product is present in products grid.';
    }
}
