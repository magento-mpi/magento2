<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Api;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Class StockStatusTest
 */
class StockStatusTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/stockStatus';

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testGetProductStockStatus()
    {
        $productSku = 'simple';
        $objectManager = Bootstrap::getObjectManager();

        /** @var \Magento\Catalog\Model\Product $product */
        $product = $objectManager->get('Magento\Catalog\Model\Product')->load(1);
        $expectedData = $product->getQuantityAndStockStatus();
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => self::RESOURCE_PATH . "/$productSku",
                'httpMethod' => RestConfig::HTTP_METHOD_GET,
            ),
            'soap' => array(
                'service' => 'catalogInventoryStockRegistryV1',
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'catalogInventoryStockRegistryV1GetStockStatusBySku',
            ),
        );

        $requestData = ['productSku' => $productSku];
        $actualData = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertArrayHasKey('stock_item', $actualData);
        $this->assertEquals($expectedData['is_in_stock'], $actualData['stock_item']['is_in_stock']);
        $this->assertEquals($expectedData['qty'], $actualData['stock_item']['qty']);
    }
}
