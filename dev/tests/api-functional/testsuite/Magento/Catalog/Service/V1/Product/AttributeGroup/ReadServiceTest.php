<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\AttributeGroup;

use Magento\TestFramework\TestCase\WebapiAbstract;

class ReadServiceTest extends WebapiAbstract
{
    const SERVICE_NAME = 'catalogProductAttributeGroupReadServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products/attribute-sets';

    public function testListGroups()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/1/groups",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetList'
            ]
        ];
        $groupData = $this->_webApiCall($serviceInfo, ['attributeSetId' => 1]);

        $this->assertCount(1, $groupData, "The group data does not match.");
        $this->assertEquals('General', $groupData[0]['name'], "The group data does not match.");
    }
}
