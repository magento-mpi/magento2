<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Service\V1;

use Magento\Webapi\Model\Rest\Config as RestConfig;
use Magento\GroupedProduct\Model\Resource\Product\Link;

class CatalogProductLinkServiceTest extends \Magento\TestFramework\TestCase\WebapiAbstract
{
    const SERVICE_NAME = 'catalogProductLinkReadServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products/';

    public function testGetList()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . 'links/types',
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetProductLinkTypes'
            ]
        ];

        $haystack = $this->_webApiCall($serviceInfo);

        /**
         * Validate that product type links provided by Magento_GroupedProduct module are present
         */
        $expectedItems = ['type' => 'associated', 'code' => Link::LINK_TYPE_GROUPED];
        $this->assertContains($expectedItems, $haystack);

    }

    /**
     * @magentoApiDataFixture Magento/GroupedProduct/_files/product_grouped.php
     */
    public function testGetLinkedProductsGroped()
    {
        $productSku = 'grouped-product';
        $linkType = 'associated';

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . $productSku . '/links/' . $linkType,
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetLinkedProducts'
            ]
        ];

        $haystack = $this->_webApiCall($serviceInfo, ['productSku' => $productSku, 'type' => $linkType]);

        $expected = [
            [
                'type' => 'simple',
                'attribute_set_id' => 4,
                'sku' => 'simple-1',
                'position' => 1
            ],
            [
                'type' => 'virtual',
                'attribute_set_id' => 4,
                'sku' => 'virtual-product',
                'position' => 2
            ]
        ];
        $this->assertEquals($expected, $haystack);
    }

    public function testGetLinkAttributes()
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
                'operation' => self::SERVICE_NAME . 'GetLinkAttributes'
            ]
        ];

        $haystack = $this->_webApiCall($serviceInfo, ['type' => $linkType]);

        $expected = [
            ['code' => 'position', 'type' => 'int'],
            ['code' => 'qty', 'type' => 'decimal'],
        ];
        $this->assertEquals($expected, $haystack);
    }
} 
