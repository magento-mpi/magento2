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
 * Assert bestseller info in report: date, product name and qty.
 */
class AssertBestsellerReportResult extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert bestseller info in report: date, product name and qty.
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
        /** @var CatalogProductSimple $product */
        $product = $order->getEntityId()['products'][0];

        $filter = [
            'date' => date($date),
            'product' => $product->getName(),
            'price' => $product->getPrice(),
            'orders' => $product->getCheckoutData()['qty'],
        ];

        \PHPUnit_Framework_Assert::assertTrue(
            $bestsellers->getGridBlock()->isRowVisible($filter),
            'Bestseller does not present in report grid.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Bestseller total result is equals to data from dataSet.';
    }
}
