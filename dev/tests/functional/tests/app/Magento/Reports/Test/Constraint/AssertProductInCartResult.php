<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Reports\Test\Page\Adminhtml\ShopCartProductReport;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class AssertProductInCartResult
 * Assert that product is present in Products in Carts report grid
 */
class AssertProductInCartResult extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that product is present in Products in Carts report grid by name, price, carts
     *
     * @param ShopCartProductReport $shopCartProductReport
     * @param CatalogProductSimple $product
     * @param string $carts
     * @return void
     */
    public function processAssert(ShopCartProductReport $shopCartProductReport, CatalogProductSimple $product, $carts)
    {
        \PHPUnit_Framework_Assert::assertTrue(
            $shopCartProductReport->open()->getGridBlock()->isProductVisible($product, $carts),
            'Product is absent in Products in Carts report grid.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Product is present in Products in Carts report grid.';
    }
}
