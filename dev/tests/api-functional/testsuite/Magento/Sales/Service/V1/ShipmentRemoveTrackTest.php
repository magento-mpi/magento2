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
 * Class ShipmentRemoveTrackTest
 */
class ShipmentRemoveTrackTest extends WebapiAbstract
{
    /**
     * Service read name
     */
    const SERVICE_READ_NAME = 'salesShipmentRemoveTrackV1';

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
     * Test shipment remove track service
     *
     * @magentoApiDataFixture Magento/Sales/_files/shipment.php
     */
    public function testShipmentRemoveTrack()
    {
        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        $shipmentCollection = $this->objectManager->get('Magento\Sales\Model\Resource\Order\Shipment\Collection');
        $shipment = $shipmentCollection->getFirstItem();

        /** @var \Magento\Sales\Model\Order\Shipment\Track $track */
        $track = $this->objectManager->create('Magento\Sales\Model\Order\Shipment\TrackFactory')->create();
        $track->addData(
            [
                ShipmentTrack::ENTITY_ID => null,
                ShipmentTrack::ORDER_ID => 12,
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
            ]
        );
        $track->save();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/shipment/track/' . $track->getId(),
                'httpMethod' => Config::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'invoke'
            ]
        ];

        $this->assertTrue($this->_webApiCall($serviceInfo, ['id' => $track->getId()]));
    }
}
