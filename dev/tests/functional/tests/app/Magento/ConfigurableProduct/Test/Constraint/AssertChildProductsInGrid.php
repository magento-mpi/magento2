<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Constraint;

use Magento\ConfigurableProduct\Test\Fixture\ConfigurableProductInjectable;
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;

/**
 * Class AssertChildProductsInGrid
 */
class AssertChildProductsInGrid extends AbstractConstraint
{
    /**
     * Default status visibility on child products
     */
    const NOT_VISIBLE_INDIVIDUALLY = 'Not Visible Individually';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that child products generated during configurable product are present in products grid
     *
     * @param CatalogProductIndex $productGrid
     * @param ConfigurableProductInjectable $product
     * @return void
     */
    public function processAssert(CatalogProductIndex $productGrid, ConfigurableProductInjectable $product)
    {
        $configurableAttributesData = $product->getConfigurableAttributesData();
        $errors = [];

        $productGrid->open();
        foreach ($configurableAttributesData['matrix'] as $variation) {
            $filter = [
                'name' => $variation['name'],
                'sku' => $variation['sku'],
                'visibility' => self::NOT_VISIBLE_INDIVIDUALLY,
            ];

            if (!$productGrid->getProductGrid()->isRowVisible($filter)) {
                $errors[] = sprintf(
                    'Child product with name: "%s" and sku:"%s" is absent in grid.',
                    $filter['name'],
                    $filter['sku']
                );
            }
        }

        \PHPUnit_Framework_Assert::assertEmpty($errors, implode($errors, ' '));
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Child products generated during configurable product are present in products grid.';
    }
}
