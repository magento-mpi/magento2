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
use Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata;
use Magento\Webapi\Exception as HTTPExceptionCodes;

/**
 * Class ProductAttributeReadServiceTest
 * @package Magento\Catalog\Service\V1
 */
class ProductAttributeWriteServiceTest extends WebapiAbstract
{
    const SERVICE_NAME = 'catalogProductAttributeWriteServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products/attributes';

    /**
     * @dataProvider createDataProvider
     */
    public function testCreate($data)
    {
        $this->assertGreaterThan(0, $this->createAttribute($data));
    }

    /**
     * @dataProvider createValidateDataProvider
     * @expectedException Exception
     */
    public function testCreateValidate($data)
    {
        $this->assertGreaterThan(0, $this->createAttribute($data));
    }

    /**
     * @depends testCreate
     */
    public function testRemove()
    {
        $attributeId = $this->createAttribute($this->getAttributeData());
        $this->assertTrue($this->deleteAttribute($attributeId));
    }

    public function testRemoveNoSuchEntityException()
    {
        $invalidId = -1;
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
            $errorObj = $this->_processRestExceptionResult($e);
            $this->assertEquals($expectedMessage, $errorObj['message']);
            $this->assertEquals(['fieldName' => 'attribute_id', 'fieldValue' => $invalidId], $errorObj['parameters']);
            $this->assertEquals(HTTPExceptionCodes::HTTP_NOT_FOUND, $e->getCode());
        }
    }

    protected function getAttributeData()
    {
        return array(
            AttributeMetadata::ATTRIBUTE_CODE => uniqid('code_'),
            AttributeMetadata::FRONTEND_LABEL => uniqid('label_'),
            AttributeMetadata::DEFAULT_VALUE => 'default value',
            AttributeMetadata::REQUIRED => true,
            AttributeMetadata::FRONTEND_INPUT => 'text',
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
            [$builder([AttributeMetadata::ATTRIBUTE_CODE => ''])],
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

    protected function createAttribute(array $data)
    {
        $serviceInfo = [
            'rest' => ['resourcePath' => self::RESOURCE_PATH, 'httpMethod' => RestConfig::HTTP_METHOD_POST],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'create'
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
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'remove'
            ]
        ];
        return $this->_webApiCall($serviceInfo, ['attributeId' => $id]);
    }

    /**
     * @param \Exception $e
     * @return array
     * <pre> ex.
     * 'message' => "No such entity with %fieldName1 = %value1, %fieldName2 = %value2"
     * 'parameters' => [
     *      "fieldName1" => "email",
     *      "value1" => "dummy@example.com",
     *      "fieldName2" => "websiteId",
     *      "value2" => 0
     * ]
     *
     * </pre>
     */
    protected function _processRestExceptionResult(\Exception $e)
    {
        $error = json_decode($e->getMessage(), true);
        //Remove line breaks and replace with space
        $error['message'] = trim(preg_replace('/\s+/', ' ', $error['message']));
        // remove trace and type, will only be present if server is in dev mode
        unset($error['trace']);
        unset($error['type']);
        return $error;
    }
}
