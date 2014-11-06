<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GroupedProduct\Api;

use Magento\Webapi\Model\Rest\Config as RestConfig;
use Magento\GroupedProduct\Model\Resource\Product\Link;

class ProductLinkTypeListTest extends \Magento\TestFramework\TestCase\WebapiAbstract
{
    const SERVICE_NAME = 'catalogProductLinkTypeListV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products/';

    public function testGetItems()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . 'links/types',
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetItems'
            ]
        ];

        $actual = $this->_webApiCall($serviceInfo);

        /**
         * Validate that product type links provided by Magento_GroupedProduct module are present
         */
        $expectedItems = ['key' => 'associated', 'value' => Link::LINK_TYPE_GROUPED];
        $this->assertContains($expectedItems, $actual);

    }

    public function testGetItemAttributes()
    {
        $linkType = 'associated';

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . 'links/' . $linkType . '/attributes',
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetItemAttributes'
            ]
        ];

        $actual = $this->_webApiCall($serviceInfo, ['type' => $linkType]);

        $expected = [
            ['key' => 'position', 'value' => 'int'],
            ['key' => 'qty', 'value' => 'decimal'],
        ];
        $this->assertEquals($expected, $actual);
    }
}
