<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

use Magento\TestFramework\TestCase\WebapiAbstract;

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
        $this->assertEquals(4, $defaultSet['attribute_set_id']);
        $this->assertEquals('Default', $defaultSet['attribute_set_name']);
    }

}
