<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\ConfigurableProduct\Test\Fixture\CatalogProductConfigurable;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;

/**
 * Class AssertConfigurableInGrid
 *
 * @package Magento\ConfigurableProduct\Test\Constraint
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
     * Assert product availability in Products Grid
     *
     * @param CatalogProductConfigurable $configurable
     * @param CatalogProductIndex $productPageGrid
     * @return void
     */
    public function processAssert(CatalogProductConfigurable $configurable, CatalogProductIndex $productPageGrid)
    {
        $filter = ['sku' => $configurable->getSku()];
        $productPageGrid->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $productPageGrid->getProductGrid()->isRowVisible($filter),
            'Product with sku \'' . $configurable->getSku() . '\' is absent in Products grid.'
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
