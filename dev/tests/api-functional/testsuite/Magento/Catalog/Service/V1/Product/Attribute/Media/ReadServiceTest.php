<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute\Media;
use Magento\TestFramework\TestCase\WebapiAbstract;

class ReadServiceTest extends WebapiAbstract
{
    public function testGetTypes()
    {
        $attributeSetId = 12; // default attribute set
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/media/types/' . $attributeSetId,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => 'catalogProductAttributeMediaReadServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'catalogProductAttributeMediaReadServiceV1GetTypes'
            ]
        ];

        $requestData = [
            'attributeSetId' => $attributeSetId
        ];
        $types = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertGreaterThan(2, count($types));
    }
}