<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

use Magento\Sales\Model\Order\ShipmentRepository;

/**
 * Class ShipmentEmail
 */
class ShipmentEmail
{
    /**
     * @var ShipmentRepository
     */
    protected $shipmentRepository;

    /**
     * @var \Magento\Shipping\Model\ShipmentNotifier
     */
    protected $notifier;

    /**
     * @param ShipmentRepository $shipmentRepository
     * @param \Magento\Shipping\Model\ShipmentNotifier $notifier
     */
    public function __construct(
        ShipmentRepository $shipmentRepository,
        \Magento\Shipping\Model\ShipmentNotifier $notifier
    ) {
        $this->shipmentRepository = $shipmentRepository;
        $this->notifier = $notifier;
    }

    /**
     * Invoke notifyUser service
     *
     * @param int $id
     * @return bool
     */
    public function invoke($id)
    {
        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        $shipment = $this->shipmentRepository->get($id);
        return $this->notifier->notify($shipment);
    }
}
