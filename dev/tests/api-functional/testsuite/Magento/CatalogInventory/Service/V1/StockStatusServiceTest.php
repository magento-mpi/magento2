<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Service\V1;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Class ProductTypeServiceTest
 */
class StockStatusServiceTest extends WebapiAbstract
{
    const SERVICE_NAME = 'catalogInventoryStockStatusServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/stockItem/';

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testGetProductStockStatus()
    {
        $sku = 'simple';
        $objectManager = Bootstrap::getObjectManager();

        /** @var \Magento\Catalog\Model\Product $product */
        $product = $objectManager->get('Magento\Catalog\Model\Product')->load(1);
        $expectedData = $product->getQuantityAndStockStatus();
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => self::RESOURCE_PATH . 'status/' . $sku,
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

    /**
     * @param float $qty
     * @param int $currentPage
     * @param int $pageSize
     * @param array $result
     * @magentoApiDataFixture Magento/Catalog/_files/multiple_products.php
     * @dataProvider getLowStockItemsDataProvider
     */
    public function testGetLowStockItems($qty, $currentPage, $pageSize, $result)
    {
        /** @var \Magento\CatalogInventory\Service\V1\Data\LowStockCriteriaBuilder $builder */
        $builder = Bootstrap::getObjectManager()->create(
            'Magento\CatalogInventory\Service\V1\Data\LowStockCriteriaBuilder'
        );
        $builder->setCurrentPage($currentPage);
        $builder->setPageSize($pageSize);
        $builder->setQty($qty);
        /** @var \Magento\CatalogInventory\Service\V1\Data\LowStockCriteria $lowStockCriteria */
        $lowStockCriteria = $builder->create();
        $requestData = ['lowStockCriteria' => $lowStockCriteria->__toArray()];
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => self::RESOURCE_PATH . 'lowStock/?' . http_build_query($requestData),
                'httpMethod' => RestConfig::HTTP_METHOD_GET,
            ),
            'soap' => array(
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetLowStockItems',
            ),
        );
        $this->assertEquals($result, $this->_webApiCall($serviceInfo, $requestData));
    }

    /**
     * @return array
     */
    public function getLowStockItemsDataProvider()
    {
        return [
            [
                100,
                1,
                10,
                [
                    'search_criteria' => ['current_page' => 1, 'page_size' => 10, 'qty' => 100],
                    'total_count' => 2,
                    'items' => ['simple1', 'simple2']
                ]
            ],
            [
                50,
                1,
                10,
                [
                    'search_criteria' => ['current_page' => 1, 'page_size' => 10, 'qty' => 50],
                    'total_count' => 1,
                    'items' => ['simple2']
                ]
            ],
            [
                49,
                1,
                10,
                [
                    'search_criteria' => ['current_page' => 1, 'page_size' => 10, 'qty' => 49],
                    'total_count' => 0,
                    'items' => []
                ]
            ],
        ];
    }
}
