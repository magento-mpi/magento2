<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Shipping\Test\Constraint;

use Mtf\ObjectManager;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Shipping\Test\Page\Adminhtml\ShipmentIndex;
use Magento\Shipping\Test\Page\Adminhtml\SalesShipmentView;

/**
 * Class AssertShipmentItems
 * Assert shipment items on shipment view page
 */
class AssertShipmentItems extends AbstractAssertItems
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Shipment index page
     *
     * @var ShipmentIndex
     */
    protected $shipmentPage;

    /**
     * @constructor
     * @param ObjectManager $objectManager
     * @param ShipmentIndex $shipmentIndex
     */
    public function __construct(ObjectManager $objectManager, ShipmentIndex $shipmentIndex)
    {
        parent::__construct($objectManager);
        $this->shipmentPage = $shipmentIndex;
    }

    /**
     * Assert shipped products are represented on shipment view page
     *
     * @param SalesShipmentView $orderShipmentView
     * @param OrderInjectable $order
     * @param array $ids
     * @param array|null $shipment [optional]
     * @return void
     */
    public function processAssert(
        SalesShipmentView $orderShipmentView,
        OrderInjectable $order,
        array $ids,
        array $shipment = null
    ) {
        $this->shipmentPage->open();
        $this->assert($order, $ids, $orderShipmentView, $shipment);
    }

    /**
     * Process assert
     *
     * @param OrderInjectable $order
     * @param array $ids
     * @param SalesShipmentView $salesShipmentView
     * @param array|null $shipment [optional]
     * @return void
     */
    protected function assert(
        OrderInjectable $order,
        array $ids,
        SalesShipmentView $salesShipmentView,
        array $shipment = null
    ) {
        $orderId = $order->getId();
        $productsData = $this->prepareOrderProducts($order, $shipment);
        foreach ($ids['shipmentIds'] as $shipmentId) {
            $filter = [
                'order_id' => $orderId,
                'id' => $shipmentId
            ];
            $this->shipmentPage->getShipmentsGrid()->searchAndOpen($filter);
            $itemsData = $this->preparePageItems($salesShipmentView->getItemsBlock()->getData());
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
        return 'All shipment products are present in shipment page.';
    }
}
