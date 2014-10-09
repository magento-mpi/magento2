<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Reports\Test\Page\Adminhtml\Bestsellers;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class AssertBestsellerReportResult
 * Assert bestseller info in report: date, product name and qty
 */
class AssertBestsellerReportResult extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert bestseller info in report: date, product name and qty
     *
     * @param Bestsellers $bestsellers
     * @param OrderInjectable $order
     * @param string $date
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function processAssert(Bestsellers $bestsellers, OrderInjectable $order, $date)
    {
        $products = $order->getEntityId()['products'];
        $totalQuantity = $bestsellers->getGridBlock()->getViewsResults($products, $date);
        $productQty = [];
        foreach ($products as $key => $product) {
            /** @var CatalogProductSimple $product*/
            $productQty[$key] = $product->getCheckoutData()['options']['qty'];
        }
        \PHPUnit_Framework_Assert::assertEquals($productQty, $totalQuantity);
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Bestseller total result is equals to data from dataSet.';
    }
}
