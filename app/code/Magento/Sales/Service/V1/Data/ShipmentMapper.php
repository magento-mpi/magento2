<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Service\V1\Data;

/**
 * Class ShipmentMapper
 */
class ShipmentMapper
{
    /**
     * @param ShipmentBuilder $shipmentBuilder
     * @param ShipmentItemMapper $shipmentItemMapper
     * @param ShipmentTrackMapper $shipmentTrackMapper
     */
    public function __construct(
        ShipmentBuilder $shipmentBuilder,
        ShipmentItemMapper $shipmentItemMapper,
        ShipmentTrackMapper $shipmentTrackMapper
    ) {
        $this->shipmentBuilder = $shipmentBuilder;
        $this->shipmentItemMapper = $shipmentItemMapper;
        $this->shipmentTrackMapper = $shipmentTrackMapper;
    }

    /**
     * Returns array of items
     *
     * @param \Magento\Sales\Model\Order\Shipment $object
     * @return ShipmentItem[]
     */
    protected function getItems(\Magento\Sales\Model\Order\Shipment $object)
    {
        $items = [];
        foreach ($object->getItemsCollection() as $item) {
            $items[] = $this->shipmentItemMapper->extractDto($item);
        }
        return $items;
    }

    /**
     * Returns array of tracks
     *
     * @param \Magento\Sales\Model\Order\Shipment $object
     * @return ShipmentTrack[]
     */
    protected function getTracks(\Magento\Sales\Model\Order\Shipment $object)
    {
        $items = [];
        foreach ($object->getTracksCollection() as $item) {
            $items[] = $this->shipmentTrackMapper->extractDto($item);
        }
        return $items;
    }

    /**
     * @param \Magento\Sales\Model\Order\Shipment $object
     * @return \Magento\Sales\Service\V1\Data\Shipment
     */
    public function extractDto(\Magento\Sales\Model\Order\Shipment $object)
    {
        $this->shipmentBuilder->populateWithArray($object->getData());
        $this->shipmentBuilder->setItems($this->getItems($object));
        $this->shipmentBuilder->setTracks($this->getTracks($object));
        $this->shipmentBuilder->setPackages(serialize($object->getPackages()));
        return $this->shipmentBuilder->create();
    }
}
