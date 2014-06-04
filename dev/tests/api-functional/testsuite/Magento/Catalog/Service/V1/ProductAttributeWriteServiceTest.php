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

    public function testRemove()
    {
        $attributeData = $this->createAttribute($this->getAttributeData());
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $attributeData[AttributeMetadata::ATTRIBUTE_ID],
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'remove'
            ]
        ];

        $response = (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) ?
            $this->_webApiCall($serviceInfo, ['id' => $attributeData[AttributeMetadata::ATTRIBUTE_ID]]) :
            $this->_webApiCall($serviceInfo);
        $this->assertTrue($response);
    }

    public function testRemoveNoSuchEntityException()
    {
        $invalidId = -1;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $invalidId,
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'remove'
            ]
        ];
        $expectedMessage = 'No such entity with %fieldName = %fieldValue';

        try {
            if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
                $this->_webApiCall($serviceInfo, ['id' => $invalidId]);
            } else {
                $this->_webApiCall($serviceInfo);
            }
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
            $this->assertEquals(['fieldName' => 'id', 'fieldValue' => $invalidId], $errorObj['parameters']);
            $this->assertEquals(HTTPExceptionCodes::HTTP_NOT_FOUND, $e->getCode());
        }
    }

    protected function getAttributeData()
    {
        return array();
    }

    protected function createAttribute(array $data)
    {
        //TODO: Replace with API call
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Resource\Eav\Attribute'
        );
        $model->setAttributeCode(
            'test'
        )->setEntityTypeId(
                \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                    'Magento\Eav\Model\Config'
                )->getEntityType(
                        'catalog_product'
                    )->getId()
            )->setFrontendLabel(
                'test'
            );
        $model->save();
        return [AttributeMetadata::ATTRIBUTE_ID => $model->getId()];
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
