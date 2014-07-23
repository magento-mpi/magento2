<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Service\V1;

use Magento\TestFramework\TestCase\WebapiAbstract,
    Magento\Webapi\Model\Rest\Config as RestConfig;

class OrderNotifyUserTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'orderNotifyUser';
    const RESOURCE_PATH = '/V1/order';

    public function testOrderNotifyUser()
    {
        $orderId = 1;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/orders/'. $orderId . '/emails',
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => 'salesOrderNotifyUserServiceV1',
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'invoke'
            ]
        ];
        $requestData = ['id' => $orderId];
        $result = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertTrue($result);
    }

} 