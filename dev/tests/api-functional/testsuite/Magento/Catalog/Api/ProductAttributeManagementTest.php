<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api;

use \Magento\Webapi\Model\Rest\Config as RestConfig;
use \Magento\Webapi\Exception as HTTPExceptionCodes;

class ProductAttributeManagementTest extends \Magento\TestFramework\TestCase\WebapiAbstract
{
    const SERVICE_NAME = 'catalogProductAttributeReadServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products/attribute-sets';

    public function testGetAttributes()
    {
        $attributeSetId = \Magento\Catalog\Api\Data\ProductAttributeInterface::DEFAULT_ATTRIBUTE_SET_ID;
        $entityTypeCode = \Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE;

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $entityTypeCode . '/' . $attributeSetId . '/attributes',
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetAttributes'
            ],
        ];
        $attributes = $this->_webApiCall($serviceInfo);

        $this->assertTrue(count($attributes) > 0);
        $this->assertArrayHasKey('attribute_code', $attributes[0]);
        $this->assertArrayHasKey('attribute_id', $attributes[0]);
        $this->assertArrayHasKey('frontend_label', $attributes[0]);
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_attribute.php
     */
    public function testUnussignAttribute()
    {
        $this->markTestIncomplete('Need complete attributeSetRepository');

        $attributeSetId = \Magento\Catalog\Api\Data\ProductAttributeInterface::DEFAULT_ATTRIBUTE_SET_ID;
        $attributeCode = 'test_attribute_code_333';

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $attributeSetId . '/attributes' . '/' . $attributeCode,
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetAttributes'
            ],
        ];
        $attributes = $this->_webApiCall($serviceInfo);
    }
}
