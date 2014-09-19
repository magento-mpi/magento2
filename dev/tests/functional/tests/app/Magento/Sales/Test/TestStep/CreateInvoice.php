<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestStep;

use Mtf\TestStep\TestStepInterface;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Sales\Test\Page\Adminhtml\OrderView;
use Magento\Sales\Test\Page\Adminhtml\OrderIndex;
use Magento\Sales\Test\Page\Adminhtml\OrderInvoiceNew;
use Magento\Sales\Test\Page\Adminhtml\OrderInvoiceView;
use Magento\Sales\Test\Page\Adminhtml\OrderShipmentView;

/**
 * Class CreateInvoice
 * Create invoice from order on backend
 */
class CreateInvoice implements TestStepInterface
{
    /**
     * Orders Page
     *
     * @var OrderIndex
     */
    protected $orderIndex;

    /**
     * Order View Page
     *
     * @var OrderView
     */
    protected $orderView;

    /**
     * Order New Invoice Page
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
     * Order shipment view page
     *
     * @var OrderShipmentView
     */
    protected $orderShipmentView;

    /**
     * OrderInjectable fixture
     *
     * @var OrderInjectable
     */
    protected $order;

    /**
     * Invoice data
     *
     * @var null/array
     */
    protected $data;

    /**
     * @construct
     * @param OrderIndex $orderIndex
     * @param OrderView $orderView
     * @param OrderInvoiceNew $orderInvoiceNew
     * @param OrderInvoiceView $orderInvoiceView
     * @param OrderInjectable $order
     * @param OrderShipmentView $orderShipmentView
     * @param null/array $data[optional]
     */
    public function __construct(
        OrderIndex $orderIndex,
        OrderView $orderView,
        OrderInvoiceNew $orderInvoiceNew,
        OrderInvoiceView $orderInvoiceView,
        OrderInjectable $order,
        OrderShipmentView $orderShipmentView,
        $data = null
    ) {
        $this->orderIndex = $orderIndex;
        $this->orderView = $orderView;
        $this->orderInvoiceNew = $orderInvoiceNew;
        $this->orderInvoiceView = $orderInvoiceView;
        $this->order = $order;
        $this->orderShipmentView = $orderShipmentView;
        $this->data = $data;
    }

    /**
     * Create invoice (with shipment optionally) for order on backend
     *
     * @return array
     */
    public function run()
    {
        $this->orderIndex->open();
        $this->orderIndex->getSalesOrderGrid()->searchAndOpen(['id' => $this->order->getId()]);
        $this->orderView->getPageActions()->invoice();
        if (!empty($this->data)) {
            $this->orderInvoiceNew->getCreateBlock()->fill($this->data, $this->order->getEntityId()['products']);
        }
        $this->orderInvoiceNew->getTotalsBlock()->submit();
        if (!empty($this->data)) {
            $successMessage = $this->orderView->getMessagesBlock()->getSuccessMessages();
        }
        $invoiceId = $this->getInvoiceId($this->order);
        if (!empty($this->data)) {
            $shipmentId = $this->getShipmentId($this->order);
        }

        return [
            'invoiceId' => $invoiceId,
            'shippingId' => isset($shipmentId) ? $shipmentId : null,
            'successMessage' => isset($successMessage) ? $successMessage : null,
        ];
    }

    /**
     * Get invoice id
     *
     * @param OrderInjectable $order
     * @return null|string
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
}
