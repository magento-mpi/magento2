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
use Magento\Sales\Test\Page\Adminhtml\OrderShipmentView;
use Magento\Shipping\Test\Page\Adminhtml\OrderShipmentNew;

/**
 * Class CreateShipping
 * Create shipping from order on backend
 */
class CreateShipping implements TestStepInterface
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
     * New Order Shipment Page
     *
     * @var OrderShipmentNew
     */
    protected $orderShipmentNew;

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
     * @construct
     * @param OrderIndex $orderIndex
     * @param OrderView $orderView
     * @param OrderShipmentNew $orderShipmentNew
     * @param OrderShipmentView $orderShipmentView
     * @param OrderInjectable $order
     */
    public function __construct(
        OrderIndex $orderIndex,
        OrderView $orderView,
        OrderShipmentNew $orderShipmentNew,
        OrderShipmentView $orderShipmentView,
        OrderInjectable $order
    ) {
        $this->orderIndex = $orderIndex;
        $this->orderView = $orderView;
        $this->orderShipmentNew = $orderShipmentNew;
        $this->orderShipmentView = $orderShipmentView;
        $this->order = $order;
    }

    /**
     * Create shipping for order on backend
     *
     * @return array
     */
    public function run()
    {
        $this->orderIndex->open();
        $this->orderIndex->getSalesOrderGrid()->searchAndOpen(['id' => $this->order->getId()]);
        $this->orderView->getPageActions()->ship();
        $this->orderShipmentNew->getShipItemsBlock()->submit();
        $shippingId = $this->getShipmentId($this->order);

        return ['shippingId' => $shippingId];
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
}
