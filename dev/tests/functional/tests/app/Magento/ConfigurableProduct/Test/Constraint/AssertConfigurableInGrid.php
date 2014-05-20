<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\ConfigurableProduct\Test\Fixture\CatalogProductConfigurable;

/**
 * Class AssertConfigurableInGrid
 */
class AssertConfigurableInGrid extends AbstractConstraint
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
     * @param CatalogProductConfigurable $product
     * @param CatalogProductIndex $productPageGrid
     * @return void
     */
    public function processAssert(CatalogProductConfigurable $product, CatalogProductIndex $productPageGrid)
    {
        $filter = ['sku' => $product->getSku()];
        $productPageGrid->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $productPageGrid->getProductGrid()->isRowVisible($filter),
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
        return 'Product is present in Products grid.';
    }
}
