<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

use Magento\TestFramework\Helper\Bootstrap,
    Magento\TestFramework\TestCase\WebapiAbstract,
    Magento\Webapi\Model\Rest\Config as RestConfig;

/**
 * Class ProductTypeServiceTest
 */
class ProductAttributeSetAttributeServiceTest extends WebapiAbstract
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
        $data['attribute_set_id'] = $attributeSetId = 'BadId';
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
                'attributeSetId' => 1,
                'data' => array(
                    'attribute_id'       => 1,
                    'attribute_group_id' => 1,
                    'attribute_set_id'   => 1,
                    'sort_order'         => 10,
                    'entity_type_id'     => 4
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
        /** @var \Magento\Catalog\Service\V1\ProductAttributeSetWriteServiceInterface $attrSetWriteService*/
        $attrSetWriteService = Bootstrap::getObjectManager()
            ->get('Magento\Catalog\Service\V1\ProductAttributeSetWriteService');
        /**  @var \Magento\Catalog\Service\V1\ProductAttributeSetReadServiceInterface $attrSetReadService*/
        $attrSetReadService = Bootstrap::getObjectManager()
            ->get('Magento\Catalog\Service\V1\ProductAttributeSetReadService');
        $builder = Bootstrap::getObjectManager()
            ->get('Magento\Catalog\Service\V1\Data\Eav\AttributeSetExtendedBuilder');
        $attrSet = $attrSetWriteService->create($builder->setName($attrSetName)->setSkeletonId(4)->create());
        $attrSets = $attrSetReadService->getList();
        $this->assertEquals($attrSetName, $attrSets[count($attrSets)-1]->getName());
        $attributes = $attrSetReadService->getAttributeList($attrSet);
        $attributeSetId = $attrSet;
        $attributeId = array_shift($attributes)->getId();
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

