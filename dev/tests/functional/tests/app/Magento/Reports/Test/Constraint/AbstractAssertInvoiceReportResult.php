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
use Magento\Reports\Test\Page\Adminhtml\SalesInvoiceReport;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AbstractAssertInvoiceReportResult
 * Abstract assert for search in invoice report grid
 */
abstract class AbstractAssertInvoiceReportResult extends AbstractConstraint
{
    /**
     * Invoice report page
     *
     * @var SalesInvoiceReport
     */
    protected $salesInvoiceReport;

    /**
     * Order
     *
     * @var OrderInjectable
     */
    protected $order;

    /**
     * @constructor
     * @param ObjectManager $objectManager
     * @param SalesInvoiceReport $salesInvoiceReport
     */
    public function __construct(ObjectManager $objectManager, SalesInvoiceReport $salesInvoiceReport)
    {
        parent::__construct($objectManager);
        $this->salesInvoiceReport = $salesInvoiceReport;
    }

    /**
     * Search in invoice report grid
     *
     * @param array $invoiceReport
     * @return void
     */
    protected function searchInInvoiceReportGrid(array $invoiceReport)
    {
        $this->salesInvoiceReport->open();
        $this->salesInvoiceReport->getMessagesBlock()->clickLinkInMessages('notice', 'here');
        $this->salesInvoiceReport->getFilterForm()->viewsReport($invoiceReport);
        $this->salesInvoiceReport->getActionBlock()->showReport();
    }

    /**
     * Prepare expected result
     *
     * @param array $expectedInvoiceData
     * @return array
     */
    protected function prepareExpectedResult(array $expectedInvoiceData)
    {
        $totalInvoice = $this->order->getPrice()[0]['grand_invoice_total'];
        $expectedInvoiceData['invoiced'] += 1;
        $expectedInvoiceData['qty'] += 1;
        $expectedInvoiceData['total-invoiced'] += $totalInvoice;

        return $expectedInvoiceData;
    }
}
