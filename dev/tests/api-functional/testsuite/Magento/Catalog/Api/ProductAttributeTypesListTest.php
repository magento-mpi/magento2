<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Catalog\Api;

use Magento\Webapi\Model\Rest\Config as RestConfig;

class ProductAttributeTypesListTest extends \Magento\TestFramework\TestCase\WebapiAbstract
{
    const SERVICE_NAME = 'catalogProductAttributeTypesListV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products/attributes';

    public function testGetItems()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/types',
                'httpMethod' => RestConfig::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetItems',
            ],
        ];
        $types = $this->_webApiCall($serviceInfo);

        $this->assertTrue(count($types) > 0);
        $this->assertArrayHasKey('value', $types[0]);
        $this->assertArrayHasKey('label', $types[0]);
        $this->assertNotNull($types[0]['value']);
        $this->assertNotNull($types[0]['label']);
    }
}
