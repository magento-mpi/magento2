<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\Constraint;

use Magento\Reports\Test\Page\Adminhtml\OrderedProductsReport;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class AssertOrderedProductResult
 * Assert product name and qty in Ordered Products report
 *
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
class AssertOrderedProductResult extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert product name and qty in Ordered Products report
     *
     * @param OrderedProductsReport $orderedProducts
     * @param OrderInjectable $order
     * @return void
     */
    public function processAssert(OrderedProductsReport $orderedProducts, OrderInjectable $order)
    {
        $products = $order->getEntityId()['products'];
        $totalQuantity = $orderedProducts->getGridBlock()->getOrdersResults($order);
        $productQty = [];

        foreach ($totalQuantity as $key => $value) {
            /** @var CatalogProductSimple $product */
            $product = $products[$key];
            $productQty[$key] = $product->getCheckoutData()['options']['qty'];
        }
        \PHPUnit_Framework_Assert::assertEquals($totalQuantity, $productQty);
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Ordered Products result is equals to data from fixture.';
    }
}
