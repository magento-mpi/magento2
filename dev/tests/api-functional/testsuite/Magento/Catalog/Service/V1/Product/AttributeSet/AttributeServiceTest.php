<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\AttributeSet;

use Magento\Catalog\Service\V1\Exception;
use Magento\TestFramework\Helper\Bootstrap,
    Magento\TestFramework\TestCase\WebapiAbstract,
    Magento\Webapi\Model\Rest\Config as RestConfig;

/**
 * Class ProductTypeServiceTest
 */
class AttributeServiceTest extends WebapiAbstract
{

    /**
     * @param int $attributeSetId
     * @param array $data
     *
     * @dataProvider addAttributeDataProvider
     */
    public function testAddAttribute($attributeSetId, $data)
    {
        $requestData = [
            'attributeSetId' => $attributeSetId,
            'data' => $data
        ];

        $entityAttributeId = $this->_webApiCall($this->_getServiceInfo($attributeSetId), $requestData);
        $this->assertNotNull($entityAttributeId);
    }

    /**
     * @param int $attributeSetId
     * @param array $data
     *
     * @dataProvider addAttributeDataProvider
     * @expectedException Exception
     * @expectedExceptionMessage Attribute set does not exist
     */
    public function testAddAttributeWrongAttributeSet($attributeSetId, $data)
    {
        $attributeSetId = 'BadId';
        $serviceInfo = $this->_getServiceInfo($attributeSetId);

        $requestData = [
            'attributeSetId' => $attributeSetId,
            'data' => $data
        ];

        $this->_webApiCall($serviceInfo, $requestData);
    }

    /**
     * @param int $attributeSetId
     * @param array $data
     *
     * @dataProvider addAttributeDataProvider
     * @expectedException Exception
     * @expectedExceptionMessage Wrong attribute set id provided
     */
    public function testAddAttributeAttributeSetOfOtherEntityType($attributeSetId, $data)
    {
        $attributeSetId = '1';
        $serviceInfo = $this->_getServiceInfo($attributeSetId);

        $requestData = [
            'attributeSetId' => $attributeSetId,
            'data' => $data
        ];

        $this->_webApiCall($serviceInfo, $requestData);
    }

    /**
     * @param int $attributeSetId
     * @param array $data
     *
     * @dataProvider addAttributeDataProvider
     * @expectedException Exception
     * @expectedExceptionMessage Attribute group does not exist
     */
    public function testAddAttributeWrongAttributeGroup($attributeSetId, $data)
    {
        $data['attribute_group_id'] = 'BadId';

        $requestData = [
            'attributeSetId' => $attributeSetId,
            'data' => $data
        ];

        $this->_webApiCall($this->_getServiceInfo($attributeSetId), $requestData);
    }

    /**
     * @param int $attributeSetId
     * @param array $data
     *
     * @dataProvider addAttributeDataProvider
     * @expectedException Exception
     * @expectedExceptionMessage Attribute does not exist
     */
    public function testAddAttributeWrongAttribute($attributeSetId, $data)
    {
        $data['attribute_id'] = 'BadId';

        $requestData = [
            'attributeSetId' => $attributeSetId,
            'data' => $data
        ];

        $this->_webApiCall($this->_getServiceInfo($attributeSetId), $requestData);
    }

    /**
     * @return array
     */
    public function addAttributeDataProvider()
    {
        return array(
            array(
                'attributeSetId' => 4,
                'data' => array(
                    'attribute_id'       => 77,
                    'attribute_group_id' => 8,
                    'sort_order'         => 3
                ),
            )
        );
    }

    /**
     * Get service settings
     *
     * @param int $attributeSetId
     * @return array
     */
    protected function _getServiceInfo($attributeSetId)
    {
        return array(
            'rest' => array(
                'resourcePath' => '/V1/products/attribute-sets/' . $attributeSetId . '/attributes',
                'httpMethod' => RestConfig::HTTP_METHOD_POST,
            ),
            'soap' => array(
                'service' => 'catalogProductAttributeSetAttributeServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'catalogProductAttributeSetAttributeServiceV1AddAttribute',
            ),
        );
    }

    public function testDeleteAttribute()
    {
        $attrSetName = 'AttributeSet' . uniqid();
        /** @var \Magento\Catalog\Service\V1\Product\AttributeSet\WriteServiceInterface $attrSetWriteService*/
        $attrSetWriteService = Bootstrap::getObjectManager()
            ->get('Magento\Catalog\Service\V1\Product\AttributeSet\WriteService');
        /**  @var \Magento\Catalog\Service\V1\Product\AttributeSet\ReadServiceInterface $attrSetReadService*/
        $attrSetReadService = Bootstrap::getObjectManager()
            ->get('Magento\Catalog\Service\V1\Product\AttributeSet\ReadService');
        $builder = Bootstrap::getObjectManager()
            ->get('Magento\Catalog\Service\V1\Data\Eav\AttributeSetExtendedBuilder');
        $attributeSetId = $attrSetWriteService->create($builder->setName($attrSetName)->setSkeletonId(4)->create());
        $createdAttributeSet = $attrSetReadService->getInfo($attributeSetId);
        $this->assertEquals($attrSetName, $createdAttributeSet->getName());
        $attributes = $attrSetReadService->getAttributeList($attributeSetId);
        $removableAttribute = array();
        foreach ($attributes as $attribute)
        {
            if($attribute->getIsUserDefined())
            {
                $removableAttribute[] = $attribute;
            }
        }
        $attributeId = array_shift($removableAttribute)->getId();
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => "/V1/products/attribute-sets/$attributeSetId/attributes/$attributeId",
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE,
            ),
            'soap' => array(
                'service' => 'catalogProductAttributeSetAttributeServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'catalogProductAttributeSetAttributeServiceV1DeleteAttribute',
            ),
        );
        $requestData = array('attributeSetId' => $attributeSetId, 'attributeId' => $attributeId);
        $this->assertEquals(true, $this->_webApiCall($serviceInfo, $requestData));
    }
}

