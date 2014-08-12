<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config;

class ShipmentGetTest extends WebapiAbstract
{
    const RESOURCE_PATH = '/V1/shipment';
    const SERVICE_READ_NAME = 'salesShipmentGetV1';
    const SERVICE_VERSION = 'V1';

    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
    }

    /**
     * @magentoApiDataFixture Magento/Sales/_files/shipment.php
     */
    public function testOrderGet()
    {
        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        $shipmentCollection = $this->objectManager->get('Magento\Sales\Model\Resource\Order\Shipment\Collection');
        $shipment = $shipmentCollection->getFirstItem();
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $shipment->getId(),
                'httpMethod' => Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'invoke'
            ]
        ];
        $result = $this->_webApiCall($serviceInfo, ['id' => $shipment->getId()]);
        $data = $result;
        $this->assertArrayHasKey('items', $result);
        $this->assertArrayHasKey('tracks', $result);
        unset($data['items']);
        unset($data['tracks']);
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = serialize($value);
            }
            $this->assertEquals($value, $shipment->getData($key), $key);
        }
        $shipmentItem = $this->objectManager->get('Magento\Sales\Model\Order\Shipment\Item');
        foreach ($result['items'] as $item) {
            $shipmentItem->load($item['entity_id']);
            foreach($item as $key => $value) {
                $this->assertEquals($value, $shipmentItem->getData($key), $key);
            }
        }
        $shipmentTrack = $this->objectManager->get('Magento\Sales\Model\Order\Shipment\Track');
        foreach ($result['tracks'] as $item) {
            $shipmentTrack->load($item['entity_id']);
            foreach($item as $key => $value) {
                $this->assertEquals($value, $shipmentTrack->getData($key), $key);
            }
        }
    }
}
