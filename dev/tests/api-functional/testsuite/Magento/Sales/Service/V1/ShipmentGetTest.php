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
    const RESOURCE_PATH = '/V1/order';
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

        $expectedShipmentData = [
            'base_subtotal' => '100.0000',
            'subtotal' => '100.0000',
            'customer_is_guest' => '1',
            'increment_id' => $shipment->getId()
        ];

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

        foreach ($expectedShipmentData as $field => $value) {
            $this->assertArrayHasKey($field, $result);
            $this->assertEquals($value, $result[$field]);
        }
//
//        $this->assertArrayHasKey('payments', $result);
//        foreach ($expectedPayments as $field => $value) {
//            $this->assertArrayHasKey($field, $result['payments'][0]);
//            $this->assertEquals($value, $result['payments'][0][$field]);
//        }
//
//        $this->assertArrayHasKey('billing_address', $result);
//        $this->assertArrayHasKey('shipping_address', $result);
//        foreach ($expectedBillingAddressNotEmpty as $field) {
//            $this->assertArrayHasKey($field, $result['billing_address']);
//
//            $this->assertArrayHasKey($field, $result['shipping_address']);
//        }
    }
}
