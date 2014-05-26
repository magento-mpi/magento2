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
         * Validate that product type links provided by Magento_Catalog module are present
         */
        $expectedItems = [
            ['type' => 'related', 'code' => Link::LINK_TYPE_RELATED],
            ['type' => 'crosssell', 'code' => Link::LINK_TYPE_CROSSSELL],
            ['type' => 'upsell', 'code' => Link::LINK_TYPE_UPSELL],
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
        $productSku = 'simple_with_cross';
        $linkType = 'crosssell';

        $this->assertLinkedProducts($productSku, $linkType);
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/products_related.php
     */
    public function testGetLinkedProductsRelated()
    {
        $productSku = 'simple_with_cross';
        $linkType = 'related';

        $this->assertLinkedProducts($productSku, $linkType);
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/products_upsell.php
     */
    public function testGetLinkedProductsUpSell()
    {
        $productSku = 'simple_with_upsell';
        $linkType = 'upsell';

        $this->assertLinkedProducts($productSku, $linkType);
    }

    /**
     * @param string $productSku
     * @param int $linkType
     */
    protected function assertLinkedProducts($productSku, $linkType)
    {
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
            ['crosssell'],
            ['upsell'],
            ['related']
        ];
    }
}
