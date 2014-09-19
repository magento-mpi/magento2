<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesArchive\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Sales\Test\Page\Adminhtml\OrderView;
use Magento\Sales\Test\Page\Adminhtml\OrderIndex;
use Magento\Sales\Test\Page\SalesOrderCreditMemoNew;
use Magento\Sales\Test\Page\Adminhtml\OrderInvoiceNew;
use Magento\Shipping\Test\Page\Adminhtml\OrderShipmentNew;
use Magento\Sales\Test\Page\Adminhtml\OrderInvoiceView;
use Magento\Sales\Test\Page\Adminhtml\OrderShipmentView;

/**
 * Test Creation for MoveToArchive
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Enable "Orders Archiving" in configuration
 * 2. Enable payment method "Check/Money Order"
 * 3. Enable shipping method Flat Rate
 * 4. Create a product
 * 5. Create a customer
 * 6. Place order (and do actions according to dataset - invoice, shipment, credit memo)
 *
 * Steps:
 * 1. Go to Admin > Sales > Orders
 * 2. Select placed orders and in the 'Actions' drop-down select 'Move to Archive' option
 * 3. Click 'Submit' button
 * 4. Perform all assertions
 *
 * @group Sales_Archive_(CS)
 * @ZephyrId MAGETWO-28235
 */
class MoveToArchiveTest extends Injectable
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
     * New Order Shipment Page
     *
     * @var OrderShipmentNew
     */
    protected $orderShipmentNew;

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
     * OrderCreditMemoNew Page
     *
     * @var SalesOrderCreditMemoNew
     */
    protected $orderCreditMemoNew;

    /**
     * Fixture Factory
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Enable "Orders Archiving", "Check/Money Order", "Flat Rate" in configuration
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
     * @param OrderShipmentNew $orderShipmentNew
     * @param OrderInvoiceView $orderInvoiceView
     * @param OrderShipmentView $orderShipmentView
     * @param SalesOrderCreditMemoNew $orderCreditMemoNew
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __inject(
        OrderIndex $orderIndex,
        OrderView $orderView,
        OrderInvoiceNew $orderInvoiceNew,
        OrderShipmentNew $orderShipmentNew,
        OrderInvoiceView $orderInvoiceView,
        OrderShipmentView $orderShipmentView,
        SalesOrderCreditMemoNew $orderCreditMemoNew,
        FixtureFactory $fixtureFactory
    ) {
        $this->orderIndex = $orderIndex;
        $this->orderView = $orderView;
        $this->orderInvoiceNew = $orderInvoiceNew;
        $this->orderShipmentNew = $orderShipmentNew;
        $this->orderInvoiceView = $orderInvoiceView;
        $this->orderShipmentView = $orderShipmentView;
        $this->orderCreditMemoNew = $orderCreditMemoNew;
        $this->fixtureFactory = $fixtureFactory;
    }

    /**
     * Move order to archive
     *
     * @param OrderInjectable $order
     * @param string $steps
     * @param string $configArchive
     * @return array
     */
    public function test(OrderInjectable $order, $steps, $configArchive)
    {
        // Preconditions
        $configPayment = $this->fixtureFactory->createByCode('configData', ['dataSet' => $configArchive]);
        $configPayment->persist();

        $order->persist();

        // Steps
        $this->orderIndex->open();
        $ids = $this->processSteps($order, $steps);
        $this->orderIndex->open();
        $this->orderIndex->getSalesOrderGrid()->massaction([['id' => $order->getId()]], 'Move to Archive');

        return ['ids' => $ids];
    }

    /**
     * Process which step to take for order
     *
     * @param OrderInjectable $order
     * @param string $steps
     * @throws \Exception
     * @return array
     */
    protected function processSteps(OrderInjectable $order, $steps)
    {
        $steps = array_diff(explode(',', $steps), ['-']);
        $ids = [];
        foreach ($steps as $step) {
            $action = str_replace(' ', '', ucwords(trim($step)));
            $methodAction = 'process' . $action;
            if (is_callable([$this, $methodAction])) {
                $ids[lcfirst($action) . 'Id'] = $this->$methodAction($order);
            } else {
                throw new \Exception('Method ' . $methodAction . ' undefined!');
            }
        }

        return $ids;
    }

    /**
     * Create invoice for order
     *
     * @param OrderInjectable $order
     * @return string
     */
    protected function processInvoice(OrderInjectable $order)
    {
        $this->orderIndex->open();
        $this->orderIndex->getSalesOrderGrid()->searchAndOpen(['id' => $order->getId()]);
        $this->orderView->getPageActions()->invoice();
        $this->orderInvoiceNew->getTotalsBlock()->submit();
        $invoiceId = $this->getInvoiceId($order);

        return $invoiceId;
    }

    /**
     * Create shipping for order
     *
     * @param OrderInjectable $order
     * @return string
     */
    protected function processShipping(OrderInjectable $order)
    {
        $this->orderIndex->open();
        $this->orderIndex->getSalesOrderGrid()->searchAndOpen(['id' => $order->getId()]);
        $this->orderView->getPageActions()->ship();
        $this->orderShipmentNew->getShipItemsBlock()->submit();
        $shipmentId = $this->getShipmentId($order);

        return $shipmentId;
    }

    /**
     * Create Credit Memo for order
     *
     * @param OrderInjectable $order
     * @return string
     */
    protected function processCreditMemo(OrderInjectable $order)
    {
        $this->orderIndex->open();
        $this->orderIndex->getSalesOrderGrid()->searchAndOpen(['id' => $order->getId()]);
        $this->orderView->getPageActions()->orderCreditMemo();
        $this->orderCreditMemoNew->getActionsBlock()->refundOffline();
        $creditMemoId = $this->getCreditMemoId($order);

        return $creditMemoId;
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
     * Get credit memo id
     *
     * @param OrderInjectable $order
     * @return null|string
     */
    protected function getCreditMemoId(OrderInjectable $order)
    {
        $this->orderView->getOrderForm()->openTab('creditmemos');
        $amount = $order->getPrice()['grand_invoice_total'];
        $filter = [
            'status' => 'Refunded',
            'amount_from' => $amount,
            'amount_to' => $amount
        ];
        $this->orderView->getOrderForm()->getTabElement('creditmemos')->getGridBlock()->search($filter);
        $creditMemoId = $this->orderView->getOrderForm()->getTabElement('creditmemos')->getGridBlock()
            ->getCreditMemoId();

        return $creditMemoId;
    }
}
