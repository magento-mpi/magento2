<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute\Option;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;

class ReadServiceTest extends WebapiAbstract
{
    const SERVICE_NAME = 'catalogProductAttributeOptionReadServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products/attributes';

    public function testOptions()
    {
        $testAttributeCode = 'quantity_and_stock_status';
        $expectedOptions = array(
            array(
                'value' => '1',
                'label' => 'In Stock',
                'default' => null,
            ),
            array(
                'value' => '0',
                'label' => 'Out of Stock',
                'default' => null,
            )
        );

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $testAttributeCode . '/options',
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Options'
            ],
        ];

        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $response = $this->_webApiCall($serviceInfo, array('id' => $testAttributeCode));
        } else {
            $response = $this->_webApiCall($serviceInfo);
        }

        $this->assertTrue(is_array($response));
        $this->assertEquals($expectedOptions, $response);
    }
} 
