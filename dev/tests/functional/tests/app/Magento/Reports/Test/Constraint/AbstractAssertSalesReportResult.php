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
 * Class AbstractAssertSalesReportResult
 * Abstract assert for search in sales report grid
 */
abstract class AbstractAssertSalesReportResult extends AbstractConstraint
{
    /**
     * Sales report page
     *
     * @var SalesReport
     */
    protected $salesReportPage;

    /**
     * Order
     *
     * @var OrderInjectable
     */
    protected $order;

    /**
     * @constructor
     * @param ObjectManager $objectManager
     * @param SalesReport $salesReportPage
     */
    public function __construct(ObjectManager $objectManager, SalesReport $salesReportPage)
    {
        parent::__construct($objectManager);
        $this->salesReportPage = $salesReportPage;
    }

    /**
     * Search in sales report grid
     *
     * @param array $salesReport
     * @return void
     */
    protected function searchInSalesReportGrid(array $salesReport)
    {
        $this->salesReportPage->open();
        $this->salesReportPage->getMessagesBlock()->clickLinkInMessages('notice', 'here');
        $this->salesReportPage->getFilterBlock()->viewsReport($salesReport);
        $this->salesReportPage->getActionBlock()->showReport();
    }

    /**
     * Prepare expected result
     *
     * @param array $initialSalesData
     * @return array
     */
    protected function prepareExpectedResult(array $initialSalesData)
    {
        $salesItems = 0;
        $invoice = $this->order->getPrice()[0]['grand_invoice_total'];
        $salesTotal = $this->order->getPrice()[0]['grand_order_total'];
        foreach ($this->order->getEntityId()['products'] as $product) {
            $salesItems += $product->getCheckoutData()['options']['qty'];
        }
        $initialSalesData['orders'] += 1;
        $initialSalesData['sales-items'] += $salesItems;
        $initialSalesData['sales-total'] += $salesTotal;
        $initialSalesData['invoiced'] += $invoice;
        return $initialSalesData;
    }
}
