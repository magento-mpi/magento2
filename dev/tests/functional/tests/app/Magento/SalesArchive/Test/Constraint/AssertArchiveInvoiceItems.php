<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\SalesArchive\Test\Constraint;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Sales\Test\Page\Adminhtml\OrderInvoiceView;
use Magento\SalesArchive\Test\Page\Adminhtml\ArchiveInvoices;

/**
 * Class AssertArchiveInvoiceItems
 * Assert invoiced product represented in invoice archive
 */
class AssertArchiveInvoiceItems extends AbstractAssertArchiveItems
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'medium';

    /**
     * Assert invoiced product represented in invoice archive:
     * - product name
     * - qty
     *
     * @param ArchiveInvoices $archiveInvoices
     * @param OrderInvoiceView $orderInvoiceView
     * @param OrderInjectable $order
     * @param array $ids
     * @return void
     */
    public function processAssert(
        ArchiveInvoices $archiveInvoices,
        OrderInvoiceView $orderInvoiceView,
        OrderInjectable $order,
        array $ids
    ) {
        $orderId = $order->getId();
        $productsData = $this->prepareOrderProducts($order);

        foreach ($ids['invoiceIds'] as $invoiceId) {
            $filter = [
                'order_id' => $orderId,
                'invoice_id' => $invoiceId
            ];

            $archiveInvoices->open();
            $archiveInvoices->getInvoicesGrid()->searchAndOpen($filter);
            $itemsData = $this->preparePageItems($orderInvoiceView->getItemsBlock()->getData());
            $error = $this->verifyData($productsData, $itemsData);
            \PHPUnit_Framework_Assert::assertEmpty($error, $error);
        }
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Invoiced are products represented in invoice archive.';
    }
}
