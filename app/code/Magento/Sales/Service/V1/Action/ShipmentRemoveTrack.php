<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

use Magento\Sales\Model\Order\Shipment\TrackRepository;

/**
 * Class ShipmentRemoveTrack
 */
class ShipmentRemoveTrack
{
    /**
     * @var \Magento\Sales\Model\Order\Shipment\TrackRepository
     */
    protected $trackRepository;

    /**
     * @param \Magento\Sales\Model\Order\Shipment\TrackRepository $trackRepository
     */
    public function __construct(TrackRepository $trackRepository)
    {
        $this->trackRepository = $trackRepository;
    }

    /**
     * Invoke shipment remove track
     *
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function invoke($id)
    {
        /** @var \Magento\Sales\Model\Order\Shipment\Track $trackModel */
        $trackModel = $this->trackRepository->get($id);
        $trackModel->delete();

        return true;
    }
}
