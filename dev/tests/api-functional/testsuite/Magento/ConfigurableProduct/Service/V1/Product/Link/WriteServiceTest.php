<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Service\V1\Product\Link;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;
use Magento\Webapi\Exception as HTTPExceptionCodes;
use Magento\Framework\Service\V1\Data\FilterBuilder;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Class WriteServiceTest
 */
class WriteServiceTest extends WebapiAbstract
{
    const SERVICE_NAME = 'configurableProductProductLinkWriteServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products/';

    public function testAddChild()
    {
        $productSku = 'Test Configurable';
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $productSku . '/children',
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'AddChild'
            ]
        ];

        $res = $this->_webApiCall($serviceInfo, ['productSku' => $productSku, 'childSku' => '40']);
        var_dump($res);
    }
}
