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
use \Magento\TestFramework\Helper\Bootstrap;

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

    public function testAssignAttribute()
    {
        $this->assertNotNull(
            $this->_webApiCall(
                $this->getAssignServiceInfo(),
                $this->getAttributeData()
            )
        );
    }

    public function testAssignAttributeWrongAttributeSet()
    {
        $payload = $this->getAttributeData();
        $payload['attributeSetId'] = -1;

        $expectedMessage = 'Attribute Set with id "' . $payload['attributeSetId'] . '" does not exist.';

        try {
            $this->_webApiCall($this->getAssignServiceInfo(), $payload);
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

    public function testAssignAttributeWrongAttributeGroup()
    {
        $payload = $this->getAttributeData();
        $payload['attributeGroupId'] = -1;
        $expectedMessage = 'Group with id "' . $payload['attributeGroupId'] . '" does not exist.';

        try {
            $this->_webApiCall($this->getAssignServiceInfo(), $payload);
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

    public function testAssignAttributeWrongAttribute()
    {
        $payload = $this->getAttributeData();
        $payload['attributeCode'] = 'badCode';
        $expectedMessage = 'Attribute with attributeCode "' . $payload['attributeCode'] . '" does not exist.';

        try {
            $this->_webApiCall($this->getAssignServiceInfo(), $payload);
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

    public function testUnassignAttribute()
    {
        $payload = $this->getAttributeData();

        //Assign attribute to attribute set
        /** @var \Magento\Eav\Model\AttributeManagement $attributeManagement */
        $attributeManagement = Bootstrap::getObjectManager()->get('\Magento\Eav\Model\AttributeManagement');
        $attributeManagement->assign(
            $payload['entityTypeCode'],
            $payload['attributeSetId'],
            $payload['attributeGroupId'],
            $payload['attributeCode'],
            $payload['sortOrder']
        );

        $serviceInfo = [
            'rest' => [
                'resourcePath' =>
                    self::RESOURCE_PATH .
                    '/' . $payload['attributeSetId'] .
                    '/attributes/' .
                    $payload['attributeCode'],
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetAttributes'
            ],
        ];
        $this->assertTrue($this->_webApiCall($serviceInfo));
    }

    protected function getAttributeData()
    {
        return [
            'attributeSetId' => \Magento\Catalog\Api\Data\ProductAttributeInterface::DEFAULT_ATTRIBUTE_SET_ID,
            'entityTypeCode' => \Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE,
            'attributeGroupId' => 8,
            'attributeCode' => 'cost',
            'sortOrder' => 3
        ];
    }

    protected function getAssignServiceInfo()
    {
        return [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/attributes',
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Assign'
            ],
        ];
    }
}
