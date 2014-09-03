<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute;

use Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata;
use Magento\Catalog\Service\V1\Data\Eav\Product\Attribute\FrontendLabel;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Exception as HTTPExceptionCodes;
use Magento\Webapi\Model\Rest\Config as RestConfig;

/**
 * Class WriteServiceTest
 *
 * @package Magento\Catalog\Service\V1\Product\Attribute
 */
class WriteServiceTest extends WebapiAbstract
{
    const SERVICE_WRITE_NAME = 'catalogProductAttributeWriteServiceV1';
    const SERVICE_READ_NAME = 'catalogProductAttributeReadServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products/attributes';

    /**
     * @magentoApiDataFixture Magento/Catalog/Model/Product/Attribute/_files/create_attribute_service.php
     * @dataProvider createDataProvider
     */
    public function testCreate($data)
    {
        $attribute = $this->createAttribute($data);
        $expected = $data['attribute_code'] ? $data['attribute_code'] : $data['frontend_label'][0]['label'];
        $this->assertEquals($expected, $attribute);
    }

    /**
     * Test update product attribute
     *
     * @magentoApiDataFixture Magento/Catalog/_files/product_attribute.php
     */
    public function testUpdate()
    {
        $attributeCode = 'test_attribute_code_333';
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

        $storeId = current($attribute[AttributeMetadata::FRONTEND_LABEL])[FrontendLabel::STORE_ID];
        $label = current($attribute[AttributeMetadata::FRONTEND_LABEL])[FrontendLabel::LABEL];
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
                        'label' => $newLabel
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
     * @dataProvider createValidateDataProvider
     * @expectedException \Exception
     */
    public function testCreateValidate($data)
    {
        $this->createAttribute($data);
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_attribute.php
     */
    public function testRemove()
    {
        $this->assertTrue($this->deleteAttribute('test_attribute_code_333'));
    }

    /**
     * @param $invalidId
     * @dataProvider removeNoSuchEntityExceptionDataProvider
     */
    public function testRemoveNoSuchEntityException($invalidId)
    {
        $expectedMessage = 'No such entity with %fieldName = %fieldValue';

        try {
            $this->deleteAttribute($invalidId);
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
            $this->assertEquals(['fieldName' => 'attribute_id', 'fieldValue' => $invalidId], $errorObj['parameters']);
            $this->assertEquals(HTTPExceptionCodes::HTTP_NOT_FOUND, $e->getCode());
        }
    }

    public function removeNoSuchEntityExceptionDataProvider()
    {
        return array(
            [-1],
            ['unexisting_attribute_id'],
        );
    }

    /**
     * @return array
     */
    public function createDataProvider()
    {
        $builder = function ($data) {
            return array_replace_recursive($this->getAttributeData(), $data);
        };
        return [
            [
                $builder(
                    [
                        AttributeMetadata::ATTRIBUTE_CODE => '',
                        AttributeMetadata::FRONTEND_LABEL => [
                            ['store_id' => 0, 'label' => 'label_attr_code3df4tr3']
                        ]
                    ]
                )
            ],
            [$builder([AttributeMetadata::FRONTEND_INPUT => 'boolean', AttributeMetadata::DEFAULT_VALUE => true])],
        ];
    }

    /**
     * @return array
     */
    public function createValidateDataProvider()
    {
        $builder = function ($data) {
            return array_replace_recursive($this->getAttributeData(), $data);
        };
        return [
            [$builder([AttributeMetadata::FRONTEND_LABEL => ''])],
            [$builder([AttributeMetadata::FRONTEND_INPUT => 'my_input_type'])],
        ];
    }

    protected function getAttributeData()
    {
        return array(
            AttributeMetadata::ATTRIBUTE_CODE => 'test_attribute_code_l',
            AttributeMetadata::FRONTEND_LABEL => [
                ['store_id' => 0, 'label' => uniqid('label_default_')]
            ],
            AttributeMetadata::DEFAULT_VALUE => 'default value',
            AttributeMetadata::REQUIRED => true,
            AttributeMetadata::FRONTEND_INPUT => 'text',
        );
    }

    protected function createAttribute(array $data)
    {
        $serviceInfo = [
            'rest' => ['resourcePath' => self::RESOURCE_PATH, 'httpMethod' => RestConfig::HTTP_METHOD_POST],
            'soap' => [
                'service' => self::SERVICE_WRITE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_WRITE_NAME . 'create'
            ],
        ];
        return $this->_webApiCall($serviceInfo, ['attributeMetadata' => $data]);
    }

    protected function deleteAttribute($id)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $id,
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_WRITE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_WRITE_NAME . 'remove'
            ]
        ];
        return $this->_webApiCall($serviceInfo, ['attributeId' => $id]);
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
