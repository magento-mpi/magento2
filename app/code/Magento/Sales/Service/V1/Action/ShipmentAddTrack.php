<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

use Magento\Sales\Service\V1\Data\ShipmentTrack;
use Magento\Sales\Model\Order\Shipment\TrackConverter;

/**
 * Class ShipmentAddTrack
 */
class ShipmentAddTrack
{
    /**
     * @var \Magento\Sales\Model\Order\Shipment\TrackConverter
     */
    protected $trackConverter;

    /**
     * @param \Magento\Sales\Model\Order\Shipment\TrackConverter $trackConverter
     */
    public function __construct(TrackConverter $trackConverter)
    {
        $this->trackConverter = $trackConverter;
    }

    /**
     * Invoke shipment add track service
     *
     * @param \Magento\Sales\Service\V1\Data\ShipmentTrack $track
     * @return bool
     * @throws \Exception
     */
    public function invoke(ShipmentTrack $track)
    {
        /** @var \Magento\Sales\Model\Order\Shipment\Track $trackModel */
        $trackModel = $this->trackConverter->getModel($track);
        $trackModel->save();

        return true;
    }
}
