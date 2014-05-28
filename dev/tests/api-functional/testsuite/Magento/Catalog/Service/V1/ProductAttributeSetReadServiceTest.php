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
            'id' => 4,
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

    public function testGetAttributeList()
    {
        $attributeSetId = 4;
        $expectedAttributes = array(
            array(
                'code' => 'name',
                'frontend_label' => 'Name',
                'default_value' => null,
                'is_required' => true,
                'is_user_defined' => false,
            ),
            array(
                'code' => 'sku',
                'frontend_label' => 'SKU',
                'default_value' => null,
                'is_required' => true,
                'is_user_defined' => false,
            ),
            array(
                'code' => 'status',
                'frontend_label' => 'Status',
                'default_value' => '1',
                'is_required' => false,
                'is_user_defined' => false,
            ),
            array(
                'code' => 'description',
                'frontend_label' => 'Description',
                'default_value' => null,
                'is_required' => false,
                'is_user_defined' => false,
            ),
        );

        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => "/V1/products/attribute-sets/{$attributeSetId}/attributes",
                'httpMethod' => RestConfig::HTTP_METHOD_GET,
            ),
            'soap' => array(
                'service' => 'catalogProductAttributeSetReadServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'catalogProductAttributeSetReadServiceV1GetAttributeList',
            ),
        );
        $requestData = array('attributeSetId' => $attributeSetId);
        $attributes = $this->_webApiCall($serviceInfo, $requestData);

        // Prepare actual data for check
        foreach ($attributes as &$attribute) {
            // Add empty default values because SOAP service does not return null values
            if (!isset($attribute['default_value'])) {
                $attribute['default_value'] = null;
            }
            // Remove attribute IDs (in order to make test more clear i.e. without hardcoded IDs)
            unset($attribute['id']);
        }
        foreach ($expectedAttributes as $expectedAttribute) {
            $this->assertContains($expectedAttribute, $attributes);
        }
    }

    /**
     * @expectedException \Exception
     */
    public function testGetAttributeListThrowsExceptionWhenAttributeSetIdIsNotValid()
    {
        // Attribute set with following attribute set ID does not exist
        $attributeSetId = 9999;

        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => "/V1/products/attribute-sets/{$attributeSetId}/attributes",
                'httpMethod' => RestConfig::HTTP_METHOD_GET,
            ),
            'soap' => array(
                'service' => 'catalogProductAttributeSetReadServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'catalogProductAttributeSetReadServiceV1GetAttributeList',
            ),
        );

        $requestData = array('attributeSetId' => $attributeSetId);
        $this->_webApiCall($serviceInfo, $requestData);
    }
}
