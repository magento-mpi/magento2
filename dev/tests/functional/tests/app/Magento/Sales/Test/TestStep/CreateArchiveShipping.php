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
use Magento\SalesArchive\Test\Page\Adminhtml\ArchiveOrders;
use Magento\Sales\Test\Page\Adminhtml\OrderShipmentView;
use Magento\Shipping\Test\Page\Adminhtml\OrderShipmentNew;

/**
 * Class CreateArchiveShipping
 * Create shipping from archive order on backend
 */
class CreateArchiveShipping implements TestStepInterface
{
    /**
     * Orders Page
     *
     * @var ArchiveOrders
     */
    protected $archiveOrders;

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
     * @param ArchiveOrders $archiveOrders
     * @param OrderView $orderView
     * @param OrderShipmentNew $orderShipmentNew
     * @param OrderShipmentView $orderShipmentView
     * @param OrderInjectable $order
     */
    public function __construct(
        ArchiveOrders $archiveOrders,
        OrderView $orderView,
        OrderShipmentNew $orderShipmentNew,
        OrderShipmentView $orderShipmentView,
        OrderInjectable $order
    ) {
        $this->archiveOrders = $archiveOrders;
        $this->orderView = $orderView;
        $this->orderShipmentNew = $orderShipmentNew;
        $this->orderShipmentView = $orderShipmentView;
        $this->order = $order;
    }

    /**
     * Create shipping for archive order on backend
     *
     * @return array
     */
    public function run()
    {
        $this->archiveOrders->open();
        $this->archiveOrders->getSalesOrderGrid()->searchAndOpen(['id' => $this->order->getId()]);
        $this->orderView->getPageActions()->ship();
        $this->orderShipmentNew->getShipItemsBlock()->submit();

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
