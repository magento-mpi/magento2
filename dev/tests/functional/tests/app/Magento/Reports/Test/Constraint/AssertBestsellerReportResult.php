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
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function processAssert(Bestsellers $bestsellers, OrderInjectable $order)
    {
        $products = $order->getEntityId()['products'];
        $totalQuantity = $bestsellers->getGridBlock()->getViewsResults($products);
        $productQty = [];
        foreach ($totalQuantity as $key => $value) {
            $productQty[$key] = $products[$key]->getCheckoutData()['qty'];
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
        return 'Bestseller total result is equals to data from dataSet.';
    }
}
