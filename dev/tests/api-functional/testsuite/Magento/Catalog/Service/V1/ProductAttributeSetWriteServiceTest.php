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
        ];
        $attributeSetId = $this->_webApiCall($serviceInfo, array('attributeSet' => $requestData));

        $this->assertGreaterThan(0, $attributeSetId);
        return $attributeSetId;
    }

    /**
     * @depends testCreate
     * @param $id
     */
    public function testDelete($id)
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
        $requestData = [
            'id' => $id,
            // attributeSetId ???
        ];

        $this->_webApiCall($serviceInfo, $requestData);
    }

    public function testUpdate()
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var \Magento\Eav\Model\Entity\Attribute\Set $originalAttributeSet */
        $originalAttributeSet = $objectManager->get('\Magento\Eav\Model\Entity\Attribute\SetFactory')->create();
        $originalAttributeSet->setEntityTypeId(4)
            ->setAttributeSetName('Custom Attribute Set')
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
        $attributeSetData = array(
            AttributeSet::ID => $attributeSetId,
            AttributeSet::NAME => 'Updated Attribute Set',
            AttributeSet::ORDER => 200,
        );

        $requestData = array('attributeSetData' => $attributeSetData);
        $targetAttributeSetId = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($attributeSetId, $targetAttributeSetId);

        $originalAttributeSet->load($attributeSetId);
        $this->assertEquals($attributeSetData[AttributeSet::NAME], $originalAttributeSet->getAttributeSetName());
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
