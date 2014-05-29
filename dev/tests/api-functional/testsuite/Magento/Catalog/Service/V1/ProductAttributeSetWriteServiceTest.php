<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

use Magento\TestFramework\TestCase\WebapiAbstract,
    Magento\TestFramework\Helper\Bootstrap,
    Magento\Catalog\Service\V1\Data\Eav\AttributeSet,
    Magento\Webapi\Model\Rest\Config as RestConfig;

class ProductAttributeSetWriteServiceTest extends WebapiAbstract
{
    public function testCreate()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/attribute-sets',
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => 'catalogProductAttributeSetWriteServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'catalogProductAttributeSetWriteServiceV1Create'
            ]
        ];
        $requestData = [
            'id' => null,
            'name' => 'attribute set' . \time(),
            'sort_order' => 10,
            'skeleton_id' => 4, // default attribute set id
        ];
        $attributeSetId = $this->_webApiCall($serviceInfo, array('attributeSet' => $requestData));

        $this->assertGreaterThan(0, $attributeSetId);
        return $attributeSetId;
    }

    /**
     * @dataProvider createNegativeProvider
     * @param $name
     * @param $id
     * @param $skeletonId
     * @param $expectedMessage
     * @return int
     */
    public function testCreateNegative($name, $id, $skeletonId, $expectedMessage)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/attribute-sets',
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => 'catalogProductAttributeSetWriteServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'catalogProductAttributeSetWriteServiceV1Create'
            ]
        ];
        $requestData = [
            'id' => $id,
            'name' => $name,
            'sort_order' => 10,
            'skeleton_id' => $skeletonId,
        ];
        try {
            //save the same attribute set again
            $this->_webApiCall($serviceInfo, array('attributeSet' => $requestData));
            $this->fail('Saving the same attribute set should throw an exception');
        } catch (\SoapFault $e) {
            $message = $e->getMessage();
        } catch(\Exception $e) {
            $message = json_decode($e->getMessage())->message;
        }
        $this->assertEquals($expectedMessage, $message);
    }

    public function createNegativeProvider()
    {
        $defaultSetId = 4;
        return array(
            'empty name' => array('', null, $defaultSetId, 'Attribute set name is empty.'),
            'existing id' => array(
                'attribute set' . time(),
                $defaultSetId,
                $defaultSetId,
                'Invalid value of "%value" provided for the %fieldName field.'
            ),
            'absent skeleton' => array(
                'attribute set_' . time(),
                null,
                null,
                'Invalid value of "%value" provided for the %fieldName field.'
            ),
        );
    }

    public function testCreateDuplicate()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/attribute-sets',
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => 'catalogProductAttributeSetWriteServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'catalogProductAttributeSetWriteServiceV1Create'
            ]
        ];
        $name = 'attribute set' . \time();
        $requestData = [
            'id' => null,
            'name' => $name,
            'sort_order' => 10,
            'skeleton_id' => 4, // default attribute set id
        ];
        $attributeSetId = $this->_webApiCall($serviceInfo, array('attributeSet' => $requestData));
        $this->assertGreaterThan(0, $attributeSetId);

        $expectedMessage = 'An attribute set with the "'. $name . '" name already exists.';
        try {
            //save the same attribute set again
            $this->_webApiCall($serviceInfo, array('attributeSet' => $requestData));
            $this->fail('Saving the same attribute set should throw an exception');
        } catch (\SoapFault $e) {
            $message = $e->getMessage();
        } catch(\Exception $e) {
            $message = json_decode($e->getMessage())->message;
        }
        $this->assertEquals($expectedMessage, $message);
    }

    /**
     * @depends testCreate
     * @param $id
     */
    public function testRemove($id)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/attribute-sets/' . $id,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => 'catalogProductAttributeSetWriteServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'catalogProductAttributeSetWriteServiceV1Remove'
            ]
        ];
        $requestData = [];

        $response = $this->_webApiCall($serviceInfo, $requestData);
        if ('rest' == strtolower(TESTS_WEB_API_ADAPTER)) {
            $this->assertTrue($response);
        }
    }

    /**
     * @dataProvider removeNegativeProvider
     * @param $id
     * @param $expectedMessage
     */
    public function testRemoveNegative($id, $expectedMessage)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/attribute-sets/' . $id,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => 'catalogProductAttributeSetWriteServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'catalogProductAttributeSetWriteServiceV1Remove'
            ]
        ];
        $requestData = [];

        try {
            $this->_webApiCall($serviceInfo, $requestData);
            $this->fail('Exception wasn\'t thrown because of wrong id');
        } catch(\SoapFault $e) {
            $message = $e->getMessage();
        } catch(\Exception $e) {
            $message = json_decode($e->getMessage())->message;
        }
        $this->assertEquals($expectedMessage, $message);
    }

    public function removeNegativeProvider()
    {
        return array(
            'absent set' => array(
                100500, // too big to have such attribute set id
                'No such entity with %fieldName = %fieldValue'
            ),
            'empty id' => array(
                'abc',
                'Invalid value of "%value" provided for the %fieldName field.'
            ),
            'wrong entity type' => array(
                1,
                'Invalid value of "%value" provided for the %fieldName field.'
            ),
        );
    }

    public function testUpdate()
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var \Magento\Eav\Model\Entity\Attribute\Set $originalAttributeSet */
        $originalAttributeSet = $objectManager->get('\Magento\Eav\Model\Entity\Attribute\SetFactory')->create();
        $originalAttributeSet->setEntityTypeId(4)
            ->setAttributeSetName('Custom Attribute Set'. \time())
            ->setSortOrder(100)
            ->save();
        $attributeSetId = $originalAttributeSet->getId();;

        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => "/V1/products/attribute-sets",
                'httpMethod' => RestConfig::HTTP_METHOD_PUT,
            ),
            'soap' => array(
                'service' => 'catalogProductAttributeSetWriteServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'catalogProductAttributeSetWriteServiceV1Update',
            ),
        );
        $updatedName = 'Updated Attribute Set' . \time();
        $attributeSetData = array(
            AttributeSet::ID => $attributeSetId,
            AttributeSet::NAME => $updatedName,
            AttributeSet::ORDER => 200,
        );

        $requestData = array('attributeSetData' => $attributeSetData);
        $targetAttributeSetId = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($attributeSetId, $targetAttributeSetId);

        $originalAttributeSet->load($attributeSetId);
        $this->assertEquals($updatedName, $originalAttributeSet->getAttributeSetName());
        $this->assertEquals($attributeSetData[AttributeSet::ORDER], $originalAttributeSet->getSortOrder());
        $originalAttributeSet->delete();
    }

    /**
     * @expectedException \Exception
     */
    public function testUpdateThrowsExceptionIfAttributeSetIdIsNotSpecified()
    {
        $serviceInfo = array(
            'soap' => array(
                'service' => 'catalogProductAttributeSetWriteServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'catalogProductAttributeSetWriteServiceV1Update',
            ),
            'rest' => array(
                'resourcePath' => "/V1/products/attribute-sets",
                'httpMethod' => RestConfig::HTTP_METHOD_PUT,
            ),
        );
        $attributeSetData = array(
            AttributeSet::ID => null,
            AttributeSet::NAME => 'Updated Attribute Set',
            AttributeSet::ORDER => 200,
        );
        $requestData = array('attributeSetData' => $attributeSetData);
        $this->_webApiCall($serviceInfo, $requestData);
    }

    /**
     * @expectedException \Exception
     */
    public function testUpdateThrowsExceptionIfAttributeSetIdIsNotValid()
    {
        $serviceInfo = array(
            'soap' => array(
                'service' => 'catalogProductAttributeSetWriteServiceV1',
                'operation' => 'catalogProductAttributeSetWriteServiceV1Update',
                'serviceVersion' => 'V1',
            ),
            'rest' => array(
                'httpMethod' => RestConfig::HTTP_METHOD_PUT,
                'resourcePath' => "/V1/products/attribute-sets",
            ),
        );
        $attributeSetData = array(
            AttributeSet::ID => 9999,
            AttributeSet::NAME => 'Updated Attribute Set',
            AttributeSet::ORDER => 100,
        );
        $requestData = array('attributeSetData' => $attributeSetData);
        $this->_webApiCall($serviceInfo, $requestData);
    }
}
