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
 * Class ShipmentLabelGet
 */
class ShipmentLabelGet
{
    /**
     * @var ShipmentRepository
     */
    protected $shipmentRepository;

    /**
     * @param ShipmentRepository $shipmentRepository
     */
    public function __construct(ShipmentRepository $shipmentRepository)
    {
        $this->shipmentRepository = $shipmentRepository;
    }

    /**
     * Invoke ShipmentLabelGet service
     *
     * @param int $id
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function invoke($id)
    {
        return (string)$this->shipmentRepository->get($id)->getShippingLabel();
    }
}
