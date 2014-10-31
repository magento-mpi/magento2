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

class CategoryAttributeRepositoryTest extends \Magento\TestFramework\TestCase\WebapiAbstract
{
    const SERVICE_NAME = 'catalogProductAttributeReadServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/categories/attributes';

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/category_attribute.php
     */
    public function testGet()
    {
        $attributeCode = 'test_attribute_code_666';
        $attribute = $this->getAttribute($attributeCode);

        $this->assertTrue(is_array($attribute));
        $this->assertArrayHasKey('attribute_id', $attribute);
        $this->assertArrayHasKey('attribute_code', $attribute);
        $this->assertEquals($attributeCode, $attribute['attribute_code']);
    }

    public function testGetList()
    {
        $this->markTestIncomplete('Need new searchCriteria');
        $searchCriteria = [
            'searchCriteria' => [],
            'entityTypeCode' => \Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE
        ];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetList'
            ],
        ];
        $attributes = $this->_webApiCall($serviceInfo, $searchCriteria);
    }

    /**
     * @param $attributeCode
     * @return array|bool|float|int|string
     */
    protected function getAttribute($attributeCode)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $attributeCode,
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Get'
            ],
        ];
        return $this->_webApiCall($serviceInfo);
    }
}
 