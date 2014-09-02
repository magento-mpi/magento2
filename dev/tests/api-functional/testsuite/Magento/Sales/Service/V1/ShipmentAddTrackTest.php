<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\Webapi\Model\Rest\Config;
use Magento\Sales\Service\V1\Data\ShipmentTrack;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Class ShipmentAddTrackTest
 */
class ShipmentAddTrackTest extends WebapiAbstract
{
    /**
     * Service read name
     */
    const SERVICE_READ_NAME = 'salesShipmentWriteV1';

    /**
     * Service version
     */
    const SERVICE_VERSION = 'V1';

    /**
     * Shipment increment id
     */
    const SHIPMENT_INCREMENT_ID = '100000001';

    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
    }

    /**
     * Test shipment add track service
     *
     * @magentoApiDataFixture Magento/Sales/_files/shipment.php
     */
    public function testShipmentAddTrack()
    {
        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        $shipmentCollection = $this->objectManager->get('Magento\Sales\Model\Resource\Order\Shipment\Collection');
        $shipment = $shipmentCollection->getFirstItem();

        $trackData = [
            ShipmentTrack::ENTITY_ID => null,
            ShipmentTrack::ORDER_ID => $shipment->getOrderId(),
            ShipmentTrack::CREATED_AT => null,
            ShipmentTrack::PARENT_ID => $shipment->getId(),
            ShipmentTrack::WEIGHT => 20,
            ShipmentTrack::QTY => 5,
            ShipmentTrack::TRACK_NUMBER => 2,
            ShipmentTrack::DESCRIPTION => 'Shipment description',
            ShipmentTrack::TITLE => 'Shipment title',
            ShipmentTrack::CARRIER_CODE => \Magento\Sales\Model\Order\Shipment\Track::CUSTOM_CARRIER_CODE,
            ShipmentTrack::CREATED_AT => null,
            ShipmentTrack::UPDATED_AT => null
        ];

        $requestData = ['track' => $trackData];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/shipment/track',
                'httpMethod' => Config::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'addTrack'
            ]
        ];

        $this->assertTrue($this->_webApiCall($serviceInfo, $requestData));
    }
}
