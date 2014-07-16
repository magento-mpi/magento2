<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Service\V1;

use Magento\Webapi\Model\Rest\Config as RestConfig;
use Magento\TestFramework\Helper\Bootstrap;

class ConfigurableProductServiceTest extends \Magento\TestFramework\TestCase\WebapiAbstract
{
    const SERVICE_NAME = 'configurableProductConfigurableProductServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/configurable-products/variation';

    /**
     * @magentoApiDataFixture Magento/ConfigurableProduct/_files/configurable_attribute.php
     */
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
        /** @var \Magento\Catalog\Service\V1\Product\Attribute\ReadServiceInterface $attributeService */
        $attributeService = Bootstrap::getObjectManager()
            ->get('\Magento\Catalog\Service\V1\Product\Attribute\ReadServiceInterface');
        $attribute = $attributeService->info('test_configurable');
        $attributeOptionValue = $attribute->getOptions()[0]->getValue();
        $data = [
            'product' => [
                'sku' => 'test',
                'price' => 10.0
            ],
            'configurableAttributes' => [
                [
                    'attribute_id' => $attribute->getAttributeId(),
                    'values' => [
                        [
                            'index' => $attributeOptionValue,
                            'price' => 100.0
                        ]
                    ]
                ]
            ]

        ];
        $actual = $this->_webApiCall($serviceInfo, $data);

        $expectedItems = [
            [
                'sku' => 'test',
                'price' => 110,
                'custom_attributes' => [
                    [
                        'attribute_code' => 'test_configurable',
                        'value' => $attributeOptionValue
                    ]
                ]
            ]
        ];
        $this->assertEquals($expectedItems, $actual);
    }
}
