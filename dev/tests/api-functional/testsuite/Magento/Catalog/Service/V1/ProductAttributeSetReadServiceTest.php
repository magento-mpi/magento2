<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

use Magento\TestFramework\TestCase\WebapiAbstract,
    Magento\Webapi\Model\Rest\Config as RestConfig;

class ProductAttributeSetReadServiceTest extends WebapiAbstract
{
    public function testGetGroups()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/attribute-sets',
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => 'catalogProductAttributeSetReadServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'catalogProductAttributeSetReadServiceV1GetList'
            ]
        ];

        $attributeSets = $this->_webApiCall($serviceInfo);
        // check default attribute set to be present and be first
        $defaultSet = reset($attributeSets);
        $this->assertNotEmpty($defaultSet);
        $this->assertEquals(4, $defaultSet['id']);
        $this->assertEquals('Default', $defaultSet['name']);
    }

    public function testGetInfo()
    {
        $expectedAttributeSet = array(
            'id' => 1,
            'name' => 'Default',
            'sort_order' => 2,
        );

        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => "/V1/products/attribute-sets/{$expectedAttributeSet['id']}",
                'httpMethod' => RestConfig::HTTP_METHOD_GET,
            ),
            'soap' => array(
                'service' => 'catalogProductAttributeSetReadServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'catalogProductAttributeSetReadServiceV1GetInfo',
            ),
        );
        $requestData = array('attributeSetId' => $expectedAttributeSet['id']);
        $attributeSetData = $this->_webApiCall($serviceInfo, $requestData);

        $this->assertEquals($expectedAttributeSet, $attributeSetData);
    }

    /**
     * @expectedException \Exception
     */
    public function testGetInfoThrowsExceptionWhenAttributeSetIdIsNotValid()
    {
        // Attribute set with following attribute set ID does not exist
        $attributeSetId = 9999;

        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => "/V1/products/attribute-sets/{$attributeSetId}",
                'httpMethod' => RestConfig::HTTP_METHOD_GET,
            ),
            'soap' => array(
                'service' => 'catalogProductAttributeSetReadServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'catalogProductAttributeSetReadServiceV1GetInfo',
            ),
        );

        $requestData = array('attributeSetId' => $attributeSetId);
        $this->_webApiCall($serviceInfo, $requestData);
    }
}
