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

/**
 * Class ShipmentCreateTest
 * @package Magento\Sales\Service\V1
 */
class ShipmentCreateTest extends WebapiAbstract
{
    const RESOURCE_PATH = '/V1/shipment';

    const SERVICE_READ_NAME = 'salesShipmentWriteV1';

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
    public function testInvoke()
    {
        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        $shipment = $this->objectManager->create('Magento\Sales\Model\Order\Shipment')->loadByIncrementId('100000001');
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => Config::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'create'
            ]
        ];
        $data = [
            'entity_id' => $shipment->getEntityId(),
            'store_id' => null,
            'total_weight' => null,
            'total_qty' => null,
            'email_sent' => null,
            'order_id' => null,
            'customer_id' => null,
            'shipping_address_id' => null,
            'billing_address_id' => null,
            'shipment_status' => null,
            'increment_id' => null,
            'created_at' => null,
            'updated_at' => null,
            'packages' => null,
            'shipping_label' => null,
            'tracks' => [],
            'items' => [],
        ];
        $result = $this->_webApiCall($serviceInfo, ['shipmentDataObject' => $data]);
        $this->assertTrue($result);
    }
}
