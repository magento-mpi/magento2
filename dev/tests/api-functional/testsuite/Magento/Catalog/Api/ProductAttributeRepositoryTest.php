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

class ProductAttributeRepositoryTest extends \Magento\TestFramework\TestCase\WebapiAbstract
{
    const SERVICE_NAME = 'catalogProductAttributeReadServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products/attributes';

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_attribute.php
     */
    public function testGet()
    {
        $attributeCode = 'test_attribute_code_333';
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
     * @magentoApiDataFixture Magento/Catalog/Model/Product/Attribute/_files/create_attribute_service.php
     */
    public function testCreate()
    {
        $attributeCode = 'label_attr_code3df4tr3';
        $attribute = $this->createAttribute($attributeCode);
        $this->assertArrayHasKey('attribute_id', $attribute);
        $this->assertEquals($attributeCode, $attribute['attribute_code']);
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_attribute.php
     */
    public function testCreateWithExceptionIfAttributeAlreadyExists()
    {
        $attributeCode = 'test_attribute_code_333';
        $expectedMessage = 'Attribute with the same code already exists.';
        try {
            $this->createAttribute($attributeCode);
            $this->fail("Expected exception");
        } catch (\SoapFault $e) {
            $this->assertContains(
                $expectedMessage,
                $e->getMessage(),
                "SoapFault does not contain expected message."
            );
        } catch (\Exception $e) {
            $errorObj = $this->processRestExceptionResult($e);
            $this->assertEquals($expectedMessage, $errorObj['message']);
            $this->assertEquals(HTTPExceptionCodes::HTTP_INTERNAL_ERROR, $e->getCode());
        }
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_attribute.php
     */
    public function testUpdate()
    {
        $attributeCode = 'test_attribute_code_333';
        $attribute = $this->getAttribute($attributeCode);

        $attributeData = [
            'attribute' => [
                'attribute_code' => $attributeCode,
                'store_frontend_labels' => [
                    ['store_id' => 0, 'label' => 'front_lbl_new']
                ],
                'default_value' => 'default value new',
                'is_required' => false,
                'frontend_input' => 'text'
            ]
        ];

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $attribute['attribute_id'],
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Save'
            ],
        ];
        $result = $this->_webApiCall($serviceInfo, $attributeData);

        $this->assertEquals($attribute['attribute_id'], $result['attribute_id']);
        $this->assertEquals($attributeCode, $result['attribute_code']);
        $this->assertEquals('default value new', $result['default_value']);
        $this->assertEquals('front_lbl_new', $result['frontend_label']);
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_attribute.php
     */
    public function testDeleteById()
    {
        $attributeCode = 'test_attribute_code_333';
        $this->assertTrue($this->deleteAttribute($attributeCode));
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_attribute.php
     */
    public function testDeleteNoSuchEntityException()
    {
        $attributeCode = 'some_test_code';
        $expectedMessage = 'Attribute with attributeCode "' . $attributeCode . '" does not exist.';

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $attributeCode,
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'deleteById'
            ],
        ];

        try {
            $this->_webApiCall($serviceInfo);
            $this->fail("Expected exception");
        } catch (\SoapFault $e) {
            $this->assertContains(
                $expectedMessage,
                $e->getMessage(),
                "SoapFault does not contain expected message."
            );
        } catch (\Exception $e) {
            $errorObj = $this->processRestExceptionResult($e);
            $this->assertEquals($expectedMessage, $errorObj['message']);
            $this->assertEquals(HTTPExceptionCodes::HTTP_NOT_FOUND, $e->getCode());
        }
    }

    /**
     * @param $attributeCode
     * @return array|bool|float|int|string
     */
    protected function createAttribute($attributeCode)
    {
        $attributeData = [
            'attribute' => [
                'attribute_code' => $attributeCode,
                'store_frontend_labels' => [
                    ['store_id' => 0, 'label' => 'front_lbl']
                ],
                'default_value' => 'default value',
                'is_required' => true,
                'frontend_input' => 'text'
            ]
        ];

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/',
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Save'
            ],
        ];
        return $this->_webApiCall($serviceInfo, $attributeData);
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

    /**
     * Delete attribute by code
     *
     * @param $attributeCode
     * @return array|bool|float|int|string
     */
    protected function deleteAttribute($attributeCode)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $attributeCode,
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'deleteById'
            ],
        ];
        return $this->_webApiCall($serviceInfo);
    }
}
