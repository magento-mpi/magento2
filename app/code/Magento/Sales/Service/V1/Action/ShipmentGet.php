<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

use Magento\Sales\Model\Order\ShipmentRepository;
use Magento\Sales\Service\V1\Data\ShipmentMapper;

/**
 * Class ShipmentGet
 */
class ShipmentGet
{
    /**
     * @var ShipmentRepository
     */
    protected $shipmentRepository;

    /**
     * @var ShipmentMapper
     */
    protected $shipmentMapper;

    /**
     * @param ShipmentRepository $shipmentRepository
     * @param ShipmentMapper $shipmentMapper
     */
    public function __construct(
        ShipmentRepository $shipmentRepository,
        ShipmentMapper $shipmentMapper
    ) {
        $this->shipmentRepository = $shipmentRepository;
        $this->shipmentMapper = $shipmentMapper;
    }

    /**
     * Invoke getShipment service
     *
     * @param int $id
     * @return \Magento\Sales\Service\V1\Data\Shipment
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function invoke($id)
    {
        return $this->shipmentMapper->extractDto($this->shipmentRepository->get($id));
    }
}
