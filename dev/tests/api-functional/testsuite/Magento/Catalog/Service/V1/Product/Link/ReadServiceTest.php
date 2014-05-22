<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\Link;

use Magento\Webapi\Model\Rest\Config as RestConfig;
use \Magento\Catalog\Model\Product\Link;

class ReadServiceTest extends \Magento\TestFramework\TestCase\WebapiAbstract
{
    const SERVICE_NAME = 'catalogCatalogProductLinkServiceV1';
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
         * Validate that product type links provided by Magento_Catalog module are present
         */
        $expectedItems = [
            ['type' => 'links_related', 'code' => Link::LINK_TYPE_RELATED],
            ['type' => 'links_crosssell', 'code' => Link::LINK_TYPE_CROSSSELL],
            ['type' => 'links_upsell', 'code' => Link::LINK_TYPE_UPSELL],
        ];

        foreach ($expectedItems as $item) {
            $this->assertContains($item, $haystack);
        }
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/products_crosssell.php
     */
    public function testGetLinkedProductsCrossSell()
    {
        $productId = 2;
        $linkType = Link::LINK_TYPE_CROSSSELL;

        $this->assertLinkedProducts($productId, $linkType);
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/products_related.php
     */
    public function testGetLinkedProductsRelated()
    {
        $productId = 2;
        $linkType = Link::LINK_TYPE_RELATED;

        $this->assertLinkedProducts($productId, $linkType);
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/products_upsell.php
     */
    public function testGetLinkedProductsUpSell()
    {
        $productId = 2;
        $linkType = Link::LINK_TYPE_UPSELL;

        $this->assertLinkedProducts($productId, $linkType);
    }

    /**
     * @param int $productId
     * @param int $linkType
     */
    protected function assertLinkedProducts($productId, $linkType)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . $productId . '/links/' . $linkType,
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetLinkedProducts'
            ]
        ];

        $haystack = $this->_webApiCall($serviceInfo, ['productId' => $productId, 'type' => $linkType]);

        $expected = [
            ['product_id' => 1, 'type' => 'simple', 'attribute_set_id' => 4, 'sku' => 'simple', 'position' => 1]
        ];
        $this->assertEquals($expected, $haystack);
    }

    /**
     * @param int $linkType
     * @dataProvider linkAttributesTypeDataProvider
     */
    public function testGetLinkAttributes($linkType)
    {
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

        $expected = ['code' => 'position', 'type' => 'int'];
        $this->assertContains($expected, $haystack);
    }

    public function linkAttributesTypeDataProvider()
    {
        return [
            'cross' => [Link::LINK_TYPE_CROSSSELL],
            'upsell' => [Link::LINK_TYPE_UPSELL],
            'related' => [Link::LINK_TYPE_RELATED],
        ];
    }
}
