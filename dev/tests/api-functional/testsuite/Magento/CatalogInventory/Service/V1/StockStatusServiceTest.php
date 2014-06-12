<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Service\V1;

use Magento\TestFramework\TestCase\WebapiAbstract,
    Magento\Webapi\Model\Rest\Config as RestConfig;

/**
 * Class ProductTypeServiceTest
 */
class StockStatusServiceTest extends WebapiAbstract
{
    const SERVICE_NAME = 'catalogInventoryStockStatusServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/stockItem/status';

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testGetProductStockStatus()
    {
        $sku = 'simple';

        /** @var \Magento\Catalog\Model\Product $product */
        $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Catalog\Model\Product')->load(1);
        $expectedData = $product->getQuantityAndStockStatus();

        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => self::RESOURCE_PATH . '/' . $sku,
                'httpMethod' => RestConfig::HTTP_METHOD_GET,
            ),
            'soap' => array(
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetProductStockStatusBySku',
            ),
        );

        $requestData = ['sku' => $sku];
        $actualData = $this->_webApiCall($serviceInfo, $requestData);

        $this->assertEquals($expectedData, $actualData);
    }
}

