<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Sales\Test\TestStep;

use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Sales\Test\Page\Adminhtml\OrderIndex;
use Magento\Sales\Test\Page\Adminhtml\OrderView;
use Magento\Shipping\Test\Page\Adminhtml\OrderShipmentNew;
use Magento\Shipping\Test\Page\Adminhtml\OrderShipmentView;
use Mtf\TestStep\TestStepInterface;

/**
 * Class CreateShipmentStep
 * Create shipping from order on backend
 */
class CreateShipmentStep implements TestStepInterface
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
     * Invoice data
     *
     * @var array|null
     */
    protected $data;

    /**
     * @construct
     * @param OrderIndex $orderIndex
     * @param OrderView $orderView
     * @param OrderShipmentNew $orderShipmentNew
     * @param OrderShipmentView $orderShipmentView
     * @param OrderInjectable $order
     * @param array|null $data [optional]
     */
    public function __construct(
        OrderIndex $orderIndex,
        OrderView $orderView,
        OrderShipmentNew $orderShipmentNew,
        OrderShipmentView $orderShipmentView,
        OrderInjectable $order,
        $data = null
    ) {
        $this->orderIndex = $orderIndex;
        $this->orderView = $orderView;
        $this->orderShipmentNew = $orderShipmentNew;
        $this->orderShipmentView = $orderShipmentView;
        $this->order = $order;
        $this->data = $data;
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
        if (!empty($this->data)) {
            $this->orderShipmentNew->getFormBlock()->fillData($this->data, $this->order->getEntityId()['products']);
        }
        $this->orderShipmentNew->getFormBlock()->submit();

        return ['shipmentIds' => $this->getShipmentIds()];
    }

    /**
     * Get shipment id
     *
     * @return array
     */
    public function getShipmentIds()
    {
        $this->orderView->getOrderForm()->openTab('shipments');
        return $this->orderView->getOrderForm()->getTabElement('shipments')->getGridBlock()->getIds();
    }
}
