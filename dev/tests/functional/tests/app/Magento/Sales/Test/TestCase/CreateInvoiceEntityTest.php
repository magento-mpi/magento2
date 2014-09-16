<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Sales\Test\Page\Adminhtml\OrderView;
use Magento\Sales\Test\Page\Adminhtml\OrderIndex;
use Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\Sales\Test\Page\Adminhtml\OrderInvoiceNew;
use Magento\Sales\Test\Page\Adminhtml\OrderInvoiceView;
use Magento\Sales\Test\Page\Adminhtml\OrderShipmentView;

/**
 * Test Creation for CreateInvoiceEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Enable payment method "Check/Money Order"
 * 2. Enable shipping method one of "Flat Rate/Free Shipping"
 * 3. Create order
 *
 * Steps:
 * 1. Go to Sales > Orders
 * 2. Select created order in the grid and open it
 * 3. Click 'Invoice' button
 * 4. Fill data according to dataSet
 * 5. Click 'Submit Invoice' button
 * 6. Perform assertions
 *
 * @group Order_Management_(CS)
 * @ZephyrId MAGETWO-28209
 */
class CreateInvoiceEntityTest extends Injectable
{
    /**
     * Order index page
     *
     * @var OrderIndex
     */
    protected $orderIndex;

    /**
     * Order view page
     *
     * @var OrderView
     */
    protected $orderView;

    /**
     * Order invoice new page
     *
     * @var OrderInvoiceNew
     */
    protected $orderInvoiceNew;

    /**
     * Order invoice view page
     *
     * @var OrderInvoiceView
     */
    protected $orderInvoiceView;

    /**
     * Customer account logout page
     *
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

    /**
     * Order shipment view page
     *
     * @var OrderShipmentView
     */
    protected $orderShipmentView;

    /**
     * Set up configuration
     *
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $configPayment = $fixtureFactory->createByCode('configData', ['dataSet' => 'checkmo']);
        $configPayment->persist();

        $configShipping = $fixtureFactory->createByCode('configData', ['dataSet' => 'flatrate']);
        $configShipping->persist();
    }

    /**
     * Injection data
     *
     * @param OrderIndex $orderIndex
     * @param OrderView $orderView
     * @param OrderInvoiceNew $orderInvoiceNew
     * @param OrderInvoiceView $orderInvoiceView
     * @param CustomerAccountLogout $customerAccountLogout
     * @param OrderShipmentView $orderShipmentView
     * @return void
     */
    public function __inject(
        OrderIndex $orderIndex,
        OrderView $orderView,
        OrderInvoiceNew $orderInvoiceNew,
        OrderInvoiceView $orderInvoiceView,
        CustomerAccountLogout $customerAccountLogout,
        OrderShipmentView $orderShipmentView
    ) {
        $this->orderIndex = $orderIndex;
        $this->orderView = $orderView;
        $this->orderInvoiceNew = $orderInvoiceNew;
        $this->orderInvoiceView = $orderInvoiceView;
        $this->customerAccountLogout = $customerAccountLogout;
        $this->orderShipmentView = $orderShipmentView;
    }

    /**
     * Create invoice
     *
     * @param OrderInjectable $order
     * @param array $invoice
     * @return array
     */
    public function test(OrderInjectable $order, array $invoice)
    {
        // Preconditions
        $order->persist();

        // Steps
        $this->orderIndex->open();
        $this->orderIndex->getSalesOrderGrid()->searchAndOpen(['id' => $order->getId()]);
        $this->orderView->getPageActions()->invoice();
        $this->orderInvoiceNew->getCreateBlock()->fill($invoice, $order->getEntityId()['products']);
        $this->orderInvoiceNew->getTotalsBlock()->submit();

        // Prepare data for asserts
        $successMessage = $this->orderView->getMessagesBlock()->getSuccessMessages();
        $invoiceId = $this->getInvoiceId($order);
        $shipmentId = $this->getShipmentId($order);

        return [
            'shipmentId' => $shipmentId,
            'invoiceId' => $invoiceId,
            'successMessage' => $successMessage
        ];
    }

    /**
     * Get invoice id
     *
     * @param OrderInjectable $order
     * @return string
     */
    protected function getInvoiceId(OrderInjectable $order)
    {
        $this->orderView->getOrderForm()->openTab('invoices');
        $amount = $order->getPrice()['grand_invoice_total'];
        $filter = [
            'status' => 'Paid',
            'amount_from' => $amount,
            'amount_to' => $amount
        ];
        $this->orderView->getOrderForm()->getTabElement('invoices')->getGridBlock()->searchAndOpen($filter);
        return trim($this->orderInvoiceView->getTitleBlock()->getTitle(), ' #');
    }

    /**
     * Get shipment id
     *
     * @param OrderInjectable $order
     * @return null|string
     */
    protected function getShipmentId(OrderInjectable $order)
    {
        $qty = $order->getTotalQtyOrdered();
        $shipmentId = null;
        if ($qty !== null) {
            $this->orderInvoiceView->getPageActions()->back();
            $this->orderView->getOrderForm()->openTab('shipments');
            $filter = [
                'qty_from' => $qty,
                'qty_to' => $qty
            ];
            $this->orderView->getOrderForm()->getTabElement('shipments')->getGridBlock()->searchAndOpen($filter);
            $shipmentId = trim($this->orderShipmentView->getTitleBlock()->getTitle(), ' #');
        }

        return $shipmentId;
    }

    /**
     * Log out
     *
     * @return void
     */
    public function tearDown()
    {
        $this->customerAccountLogout->open();
    }
}
