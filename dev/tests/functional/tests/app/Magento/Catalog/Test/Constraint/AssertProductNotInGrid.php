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
 * Class AssertProductNotInGrid
 */
class AssertProductNotInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that product cannot be found by name and sku.
     *
     * @param FixtureInterface $product
     * @param CatalogProductIndex $productGrid
     * @return void
     */
    public function processAssert(FixtureInterface $product, CatalogProductIndex $productGrid)
    {
        $name = $product->getName();
        $sku = $product->getSku();
        $filter = ['sku' => $sku, 'name' => $name];
        $productGrid->open();
        \PHPUnit_Framework_Assert::assertFalse(
            $productGrid->getProductGrid()->isRowVisible($filter),
            'Product with sku "' . $sku . '" and name "' . $name . '" is attend in Products grid.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Product is absent in products grid.';
    }
}
