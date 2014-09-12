<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

use Magento\Sales\Service\V1\Data\ShipmentConverter;

/**
 * Class ShipmentCreate
 *
 */
class ShipmentCreate
{
    /**
     * @var ShipmentConverter
     */
    protected $shipmentConverter;

    /**
     * @var \Magento\Framework\Logger
     */
    protected $logger;

    /**
     * @param ShipmentConverter $shipmentConverter
     * @param \Magento\Framework\Logger $logger
     */
    public function __construct(ShipmentConverter $shipmentConverter, \Magento\Framework\Logger $logger)
    {
        $this->shipmentConverter = $shipmentConverter;
        $this->logger = $logger;
    }

    /**
     * Invoke CreateShipment service
     *
     * @param \Magento\Sales\Service\V1\Data\Shipment $shipmentDataObject
     * @return bool
     * @throws \Exception
     */
    public function invoke(\Magento\Sales\Service\V1\Data\Shipment $shipmentDataObject)
    {
        try {
            /** @var \Magento\Sales\Model\Order\Shipment $shipment */
            $shipment = $this->shipmentConverter->getModel($shipmentDataObject);
            if (!$shipment) {
                return false;
            }
            $shipment->getOrder()->setIsInProcess(true);
            $shipment->register();
            $shipment->save();
            return true;
        } catch (\Exception $e) {
            $this->logger->logException($e);
            throw new \Exception(__('An error has occurred during creating Shipment'));
        }
    }
}
