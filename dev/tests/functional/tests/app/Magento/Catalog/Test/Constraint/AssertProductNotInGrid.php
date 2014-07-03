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
 * Assert that Product absence on grid
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
     * @param CatalogProductIndex $productIndexPage
     * @return void
     */
    public function processAssert(FixtureInterface $product, CatalogProductIndex $productIndexPage)
    {
        $filter = ['sku' => $product->getSku(), 'name' => $product->getName()];
        $productIndexPage->open();
        \PHPUnit_Framework_Assert::assertFalse(
            $productIndexPage->getProductGrid()->isRowVisible($filter),
            'Product with name "' . $filter['name'] . '" and sku "' . $filter['sku'] . '" is present in products grid.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Product is absent in products grid.';
    }
}
