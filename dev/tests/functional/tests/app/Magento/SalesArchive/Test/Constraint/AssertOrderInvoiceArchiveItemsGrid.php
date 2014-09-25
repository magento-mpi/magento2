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
use Mtf\Constraint\AbstractAssertForm;

/**
 * Class AssertOrderInvoiceArchiveItemsGrid
 * Assert invoiced product represented in invoice archive
 */
class AssertOrderInvoiceArchiveItemsGrid extends AbstractAssertForm
{
    /**
     * Key for sort data
     *
     * @var string
     */
    protected $sortKey = "::sku";

    /**
     * List compare fields
     *
     * @var array
     */
    protected $compareFields = [
        'product',
        'sku',
        'qty'
    ];

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
     * @param OrderInvoiceView $OrderInvoiceView
     * @param OrderInjectable $order
     * @param array $ids
     * @return void
     */
    public function processAssert(
        ArchiveInvoices $archiveInvoices,
        OrderInvoiceView $OrderInvoiceView,
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
            $itemsData = $this->prepareInvoiceItem($OrderInvoiceView->getItemsBlock()->getData());
            $error = $this->verifyData($productsData, $itemsData);
            \PHPUnit_Framework_Assert::assertEmpty($error, $error);
        }
    }

    /**
     * Prepare order products
     *
     * @param OrderInjectable $order
     * @return array
     */
    protected function prepareOrderProducts(OrderInjectable $order)
    {
        $products = $order->getEntityId()['products'];
        $productsData = [];

        /** @var CatalogProductSimple $product */
        foreach ($products as $product) {
            $productsData[] = [
                'product' => $product->getName(),
                'sku' => $product->getSku(),
                'qty' => $product->getCheckoutData()['options']['qty']
            ];
        }

        return $this->sortDataByPath($productsData, $this->sortKey);
    }

    /**
     * Prepare invoice data
     *
     * @param array $itemsData
     * @return array
     */
    protected function prepareInvoiceItem(array $itemsData)
    {
        foreach ($itemsData as $key => $itemData) {
            $itemsData[$key] = array_intersect_key($itemData, array_flip($this->compareFields));
        }
        return $this->sortDataByPath($itemsData, $this->sortKey);
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Invoiced products represented in invoice archive.';
    }
}
