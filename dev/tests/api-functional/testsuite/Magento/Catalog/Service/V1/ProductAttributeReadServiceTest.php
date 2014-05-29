<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;
use Magento\Webapi\Exception as HTTPExceptionCodes;

/**
 * Class ProductAttributeReadServiceTest
 * @package Magento\Catalog\Service\V1
 */
class ProductAttributeReadServiceTest extends WebapiAbstract
{
    const SERVICE_NAME = 'catalogProductAttributeReadServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products/attributes';

    /**
     * Checks retrieving product attribute types
     */
    public function testTypes()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/types',
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'catalogProductAttributeReadServiceV1Types'
            ],
        ];

        $types = $this->_webApiCall($serviceInfo);
        $this->assertGreaterThan(0, count($types), "The number of product attribute types returned is zero.");
    }

    /**
     * @dataProvider infoDataProvider
     * @param $attributeCode
     */
    public function testInfo($attributeCode)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $attributeCode,
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Info'
            ],
        ];

        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $response = $this->_webApiCall($serviceInfo, array('id' => $attributeCode));
        } else {
            $response = $this->_webApiCall($serviceInfo);
        }
        $this->assertTrue(is_array($response));
        $this->assertArrayHasKey('attribute_id', $response);
        $this->assertArrayHasKey('attribute_code', $response);
    }

    public function infoDataProvider()
    {
        return array(
            array('price'),
            array(95),
        );
    }
}
