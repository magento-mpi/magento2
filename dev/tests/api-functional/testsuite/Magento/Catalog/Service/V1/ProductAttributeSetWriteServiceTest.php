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
    }
}