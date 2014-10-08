<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\TestCase;

use Magento\Reports\Test\Page\Adminhtml\SalesInvoiceReport;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for SalesInvoiceReportEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Open Backend
 * 2. Go to Reports > Sales > Invoiced
 * 3. Refresh statistic
 * 4. Configure filter
 * 5. Click "Show Report"
 * 6. Save/remember report result
 * 7. Create customer
 * 8. Place order
 * 9. Create Invoice
 * 10. Refresh statistic
 *
 * Steps:
 * 1. Open Backend
 * 2. Go to Reports > Sales > Invoiced
 * 3. Configure filter
 * 4. Click "Show Report"
 * 5. Perform all assertions
 *
 * @group Reports_(MX)
 * @ZephyrId MAGETWO-29216
 */
class SalesInvoiceReportEntityTest extends Injectable
{
    /**
     * Sales Invoice Report page
     *
     * @var SalesInvoiceReport
     */
    protected $salesInvoiceReport;

    /**
     * Inject page
     *
     * @param SalesInvoiceReport $salesInvoiceReport
     * @return void
     */
    public function __inject(SalesInvoiceReport $salesInvoiceReport)
    {
        $this->salesInvoiceReport = $salesInvoiceReport;
    }

    /**
     * Sales invoice report
     *
     * @param OrderInjectable $order
     * @param array $invoiceReport
     * @return array
     */
    public function test(OrderInjectable $order, array $invoiceReport)
    {
        // Preconditions
        $this->salesInvoiceReport->open();
        $this->salesInvoiceReport->getMessagesBlock()->clickLinkInMessages('notice', 'here');
        $this->salesInvoiceReport->getFilterForm()->viewsReport($invoiceReport);
        $this->salesInvoiceReport->getActionBlock()->showReport();
        $initialInvoiceResult = $this->salesInvoiceReport->getGridBlock()->getLastInvoiceResult();
        $initialInvoiceTotalResult = $this->salesInvoiceReport->getGridBlock()->getInvoiceTotalResult();
        $order->persist();
        $invoice = $this->objectManager->create('Magento\Sales\Test\TestStep\CreateInvoiceStep', ['order' => $order]);
        $invoice->run();

        return [
            'initialInvoiceResult' => $initialInvoiceResult,
            'initialInvoiceTotalResult' => $initialInvoiceTotalResult
        ];
    }
}
