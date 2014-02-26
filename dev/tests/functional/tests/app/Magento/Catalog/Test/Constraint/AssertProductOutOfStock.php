<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class AssertProductOutOfStock
 *
 * @package Magento\Catalog\Test\Constraint
 */
class AssertProductOutOfStock extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Text value for checking Stock Availability
     */
    const STOCK_AVAILABILITY = 'Out of stock';

    /**
     * Assert  that Out of Stock status is displayed on product page
     *
     * @param CatalogProductView $catalogProductView
     * @param CatalogProductSimple $product
     * @return void
     */
    public function processAssert(CatalogProductView $catalogProductView, CatalogProductSimple $product)
    {
        $catalogProductView->init($product);
        $catalogProductView->open();
        \PHPUnit_Framework_Assert::assertEquals(
            self::STOCK_AVAILABILITY,
            $catalogProductView->getViewBlock()->stockAvailability(),
            'Control \'' . self::STOCK_AVAILABILITY . '\' is not visible.'
        );
    }

    /**
     * Text of Out of Stock assert
     *
     * @return string
     */
    public function toString()
    {
        return 'Out of stock control is visible.';
    }
}
