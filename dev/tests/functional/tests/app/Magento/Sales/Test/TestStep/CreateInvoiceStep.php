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
 * Class CreateInvoiceStep
 * Create invoice from order on backend
 */
class CreateInvoiceStep implements TestStepInterface
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
     * @var array|null
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
     * @param array|null $data[optional]
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
        $invoiceIds = $this->getInvoiceIds();
        if (!empty($this->data)) {
            $shipmentIds = $this->getShipmentIds();
        }

        return [
            'invoiceIds' => $invoiceIds,
            'shipmentIds' => isset($shipmentIds) ? $shipmentIds : null,
        ];
    }

    /**
     * Get invoice ids
     *
     * @return array
     */
    protected function getInvoiceIds()
    {
        $this->orderView->getOrderForm()->openTab('invoices');
        return $this->orderView->getOrderForm()->getTabElement('invoices')->getGridBlock()->getIds();
    }

    /**
     * Get shipment ids
     *
     * @return array
     */
    protected function getShipmentIds()
    {
        $this->orderView->getOrderForm()->openTab('shipments');
        return $this->orderView->getOrderForm()->getTabElement('shipments')->getGridBlock()->getIds();
    }
}
