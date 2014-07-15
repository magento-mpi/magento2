<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Service\V1;

use Magento\Webapi\Model\Rest\Config as RestConfig;

class ConfigurableProductServiceTest extends \Magento\TestFramework\TestCase\WebapiAbstract
{
    const SERVICE_NAME = 'configurableProductConfigurableProductServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/configurableProduct/variation';

    public function testGetVariation()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GenerateVariation'
            ]
        ];

        $data = [
            "product" => [
                "sku" => "test",
                "price" => 10.0
            ],
            "configurableAttributes" => [
                [
                    "attribute_id" => 174,
                    "values" => [
                        [
                            "index" => 14,
                            "price" => 100.0
                        ]
                    ]
                ]
            ]

        ];
        $actual = $this->_webApiCall($serviceInfo, $data);

        /**
         * Validate that product type links provided by Magento_GroupedProduct module are present
         */
        $expectedItems = [
            [
                "sku" => "test",
                "price" => 110,
                "custom_attributes" => [
                    [
                        "attribute_code" => "dd",
                        "value" => "14"
                    ]
                ]
            ]
        ];
        $this->assertEquals($expectedItems, $actual);
    }
}
