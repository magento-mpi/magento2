<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesArchive\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\SalesArchive\Test\Page\Adminhtml\ArchiveInvoices;

/**
 * Class AssertOrderInvoiceArchivedInGrid
 * Invoice with corresponding fixture data is present in Sales Archive Invoices grid
 */
class AssertOrderInvoiceArchivedInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Invoice with corresponding data is present in Sales Archive Invoices grid
     *
     * @param ArchiveInvoices $archiveInvoices
     * @param OrderInjectable $order
     * @param array $ids
     * @return void
     */
    public function processAssert(ArchiveInvoices $archiveInvoices, OrderInjectable $order, array $ids)
    {
        $orderId = $order->getId();
        $archiveInvoices->open();

        foreach ($ids['invoiceIds'] as $invoiceId) {
            $filter = [
                'order_id' => $orderId,
                'invoice_id' => $invoiceId
            ];

            $errorMessage = implode(', ', $filter);
            \PHPUnit_Framework_Assert::assertTrue(
                $archiveInvoices->getInvoicesGrid()->isRowVisible($filter),
                'Invoice with following data \'' . $errorMessage . '\' is absent in archive invoices grid.'
            );
        }
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Invoice is present in archive invoices grid.';
    }
}
