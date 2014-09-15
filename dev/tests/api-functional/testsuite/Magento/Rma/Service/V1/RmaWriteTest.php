<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Service\V1;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;

/**
 * Class RmaWriteTest
 * @package Magento\Rma\Service\V1
 */
class RmaWriteTest extends WebapiAbstract
{
    /**#@+
     * Constants defined for Web Api call
     */
    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'rmaRmaWriteV1';
    /**#@-*/

    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $objectManager;

    /**
     * @magentoApiDataFixture Magento/Rma/_files/rma.php
     */
    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
    }

    /**
     * @dataProvider addTrackDataProvider
     */
    public function testAddTrack($carrierCode, $carrierTitle)
    {
        $rma = $this->objectManager->get('Magento\Rma\Model\Rma')->load('1');
        $rmaId = $rma->getId();
        $requestData = [
            'rmaId' => $rmaId,
            'trackNumber' => 'number123',
            'carrierCode' => $carrierCode,
            'carrierTitle' => $carrierTitle
        ];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/returns/' . $rmaId . '/tracking-numbers/?' . http_build_query($requestData),
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'addTrack'
            ]
        ];
        $result = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertTrue($result);
    }

    /**
     * @return array
     */
    public function addTrackDataProvider()
    {
        return [
            [null, 'Some Title'],
            ['carrier_code', ''],
        ];
    }

    public function testRemoveTrackById()
    {
        $rma = $this->objectManager->get('Magento\Rma\Model\Rma')->load('1');
        $rmaId = $rma->getId();
        $shippingModel = $this->objectManager->get('Magento\Rma\Model\Shipping')->load('1');
        $trackId = $shippingModel->getId();
        $requestData = [
            'rmaId' => $rmaId,
            'trackId' => $trackId
        ];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/returns/' . $rmaId . '/tracking-numbers/' . $trackId
                    . '/?' . http_build_query($requestData),
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'removeTrackById'
            ]
        ];
        $result = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertTrue($result);
    }
}
