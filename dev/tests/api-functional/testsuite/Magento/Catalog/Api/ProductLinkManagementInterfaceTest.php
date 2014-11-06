<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;
use Magento\Catalog\Api\Data\ProductLinkInterface;
use Magento\TestFramework\Helper\Bootstrap;

class ProductLinkManagementInterfaceTest extends WebapiAbstract
{
    const SERVICE_NAME = 'catalogProductLinkRepositoryInterfaceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products/';

    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();
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

        $actual = $this->_webApiCall($serviceInfo, ['productSku' => $productSku, 'type' => $linkType]);

        $this->assertEquals('simple', $actual[0][ProductLinkInterface::LINKED_PRODUCT_TYPE ]);
        $this->assertEquals('simple', $actual[0][ProductLinkInterface::LINKED_PRODUCT_SKU ]);
        $this->assertEquals(1, $actual[0][ProductLinkInterface::POSITION]);
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoApiDataFixture Magento/Catalog/_files/product_virtual.php
     */
    public function testAssign()
    {
        $linkType = 'related';
        $productSku = 'simple';
        $linkData = [
            ProductLinkInterface::LINKED_PRODUCT_TYPE => 'virtual',
            ProductLinkInterface::LINKED_PRODUCT_SKU => 'virtual-product',
            ProductLinkInterface::POSITION => 100,
            ProductLinkInterface::PRODUCT_SKU => 'simple',
            ProductLinkInterface::LINK_TYPE => 'related',
        ];

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . $productSku . '/links/' . $linkType,
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'SetProductLinks'
            ]
        ];

        $arguments = [
            'productSku' => $productSku,
            'items' => [$linkData],
            'type' => $linkType
        ];

        $this->_webApiCall($serviceInfo, $arguments);
        $actual = $this->getLinkedProducts($productSku, 'related');
        array_walk($actual, function (&$item) {
            $item = $item->__toArray();
        });
        $this->assertEquals([$linkData], $actual);
    }

    /**
     * Get list of linked products
     *
     * @param string $sku
     * @param string $linkType
     * @return \Magento\Catalog\Api\Data\ProductLinkInterface[]
     */
    protected function getLinkedProducts($sku, $linkType)
    {
        /** @var \Magento\Catalog\Model\ProductLink\Management $linkManagement */
        $linkManagement = $this->objectManager->get('\Magento\Catalog\Api\ProductLinkManagementInterface');
        $linkedProducts = $linkManagement->getLinkedItemsByType($sku, $linkType);

        return $linkedProducts;
    }



}
