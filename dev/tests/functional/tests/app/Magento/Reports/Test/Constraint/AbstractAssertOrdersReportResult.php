<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\Constraint;

use Mtf\ObjectManager;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Reports\Test\Page\Adminhtml\SalesReport;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AbstractAssertOrdersReportResult
 * Abstract assert for search in report grid
 */
abstract class AbstractAssertOrdersReportResult extends AbstractConstraint
{
    /**
     * Sales report page
     *
     * @var SalesReport
     */
    protected $orderReportPage;

    /**
     * Order
     *
     * @var OrderInjectable
     */
    protected $order;

    /**
     * Search in sales report grid
     *
     * @param array $salesReport
     * @return void
     */
    protected function searchInSalesReportGrid(array $salesReport)
    {
        $this->orderReportPage->open();
        $this->orderReportPage->getMessagesBlock()->clickLinkInMessages('notice', 'here');
        $this->orderReportPage->getFilterBlock()->viewsReport($salesReport);
        $this->orderReportPage->getActionBlock()->showReport();
    }

    /**
     * Prepare expected result
     *
     * @param array $expectedSalesData
     * @return array
     */
    protected function prepareExpectedResult(array $expectedSalesData)
    {
        $salesItems = 0;
        $invoice = $this->order->getPrice()[0]['grand_invoice_total'];
        $salesTotal = $this->order->getPrice()[0]['grand_order_total'];
        foreach ($this->order->getEntityId()['products'] as $product) {
            $salesItems += $product->getCheckoutData()['options']['qty'];
        }
        $expectedSalesData['orders'] += 1;
        $expectedSalesData['sales-items'] += $salesItems;
        $expectedSalesData['sales-total'] += $salesTotal;
        $expectedSalesData['invoiced'] += $invoice;
        return $expectedSalesData;
    }
}
