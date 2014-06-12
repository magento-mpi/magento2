<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;
use Magento\Webapi\Exception as HTTPExceptionCodes;
use Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata;
use Magento\Catalog\Service\V1\Data\Eav\Product\Attribute\FrontendLabel;

/**
 * Class WriteServiceTest
 * @package Magento\Catalog\Service\V1\Product\Attribute
 */
class WriteServiceTest extends WebapiAbstract
{
    const SERVICE_WRITE_NAME = 'catalogProductAttributeWriteServiceV1';
    const SERVICE_READ_NAME = 'catalogProductAttributeReadServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products/attributes';

    /**
     * Test update product attribute
     */
    public function testUpdate()
    {
        $attributeCode = 'color';
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $attributeCode,
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_WRITE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_WRITE_NAME . 'update'
            ]
        ];

        $attribute = $this->getAttributeInfo($attributeCode);

        $this->assertTrue(is_array($attribute));
        $this->assertArrayHasKey(AttributeMetadata::FRONTEND_LABEL, $attribute);
        $this->assertTrue(is_array($attribute[AttributeMetadata::FRONTEND_LABEL]));
        $this->assertArrayHasKey(FrontendLabel::STORE_ID, current($attribute[AttributeMetadata::FRONTEND_LABEL]));
        $this->assertArrayHasKey(FrontendLabel::LABEL, current($attribute[AttributeMetadata::FRONTEND_LABEL]));

        $storeId  = current($attribute[AttributeMetadata::FRONTEND_LABEL])[FrontendLabel::STORE_ID];
        $label    = current($attribute[AttributeMetadata::FRONTEND_LABEL])[FrontendLabel::LABEL];
        $newLabel = uniqid('Color-');

        $this->assertNotEquals($label, $newLabel);

        $requestData = [
            'id' => $attributeCode,
            'attribute' => [
                AttributeMetadata::FILTERABLE => 2,
                AttributeMetadata::USED_FOR_SORT_BY => true,
                AttributeMetadata::FRONTEND_LABEL => [
                    [
                        'store_id' => $storeId,
                        'label'    => $newLabel
                    ],
                ]
            ]
        ];
        $response = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($attributeCode, $response);

        $attribute = $this->getAttributeInfo($attributeCode);
        $this->assertEquals($newLabel, current($attribute[AttributeMetadata::FRONTEND_LABEL])[FrontendLabel::LABEL]);
    }

    /**
     * Retrieve attribute info
     *
     * @param  string $attributeCode
     * @return array|bool|float|int|string
     */
    private function getAttributeInfo($attributeCode)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $attributeCode,
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'Info'
            ],
        ];

        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $response = $this->_webApiCall($serviceInfo, array('id' => $attributeCode));
        } else {
            $response = $this->_webApiCall($serviceInfo);
        }
        return $response;
    }
}
