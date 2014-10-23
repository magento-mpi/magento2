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
 * Assert that product is present in products grid.
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
     * Assert that product is present in products grid and can be found by sku, type, status and attribute set.
     *
     * @param FixtureInterface $product
     * @param CatalogProductIndex $productGrid
     * @return void
     */
    public function processAssert(FixtureInterface $product, CatalogProductIndex $productGrid)
    {
        $productGrid->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $productGrid->getProductGrid()->isRowVisible($this->prepareFilter($product)),
            'Product \'' . $product->getName() . '\' is absent in Products grid.'
        );
    }

    /**
     * Prepare filter for product grid.
     *
     * @param FixtureInterface $product
     * @return array
     */
    protected function prepareFilter(FixtureInterface $product)
    {
        $config = $product->getDataConfig();
        $productStatus = ($product->getStatus() === null || $product->getStatus() === 'Product online')
            ? 'Enabled'
            : 'Disabled';
        $filter = [
            'type' => ucfirst($config['create_url_params']['type']) . ' Product',
            'sku' => $product->getSku(),
            'status' => $productStatus,
        ];
        if (method_exists($product, 'getAttributeSetId')) {
            $filter['set_name'] = $product->getAttributeSetId();
        }

        return $filter;
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
