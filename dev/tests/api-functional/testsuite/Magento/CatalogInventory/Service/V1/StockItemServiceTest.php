<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Service\V1;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Class StockItemServiceTest
 */
class StockItemServiceTest extends WebapiAbstract
{
    const SERVICE_NAME = 'stockItemServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/stockItem';

    /** @var \Magento\CatalogInventory\Service\V1\StockItemServiceInterface */
    protected $stockItemService;

    /**
     * Execute per test initialization.
     */
    public function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->stockItemService = $objectManager->get(
            'Magento\CatalogInventory\Service\V1\StockItemServiceInterface',
            []
        );
    }

    /**
     * Execute per test cleanup.
     */
    public function tearDown()
    {
        unset($this->stockItemService);
    }

    /**
     * Verify the retrieval of a customer group by Id.
     *
     * @magentoApiDataFixture Magento/Catalog/_files/multiple_products.php
     */
    public function testGetStockItemBySku()
    {
        $productSku = 'simple1';
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/$productSku",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'stockItemServiceV1GetProduct'
            ]
        ];
        $arguments = ['sku' => $productSku];
        $stockData = $this->_webApiCall($serviceInfo, $arguments);
        $result = [
            'product_id' => 10,
            'stock_id' => 1,
            'qty' => '100.0000',
            'min_qty' => '0.0000',
            'use_config_min_qty' => 1,
            'is_qty_decimal' => 0,
            'backorders' => 0,
            'use_config_backorders' => 1,
            'min_sale_qty' => '1.0000',
            'use_config_min_sale_qty' => 1,
            'max_sale_qty' => '0.0000',
            'use_config_max_sale_qty' => 1,
            'is_in_stock' => 1,
            'low_stock_date' => '',
            'notify_stock_qty' => '',
            'use_config_notify_stock_qty' => 1,
            'manage_stock' => 0,
            'use_config_manage_stock' => 1,
            'stock_status_changed_auto' => 0,
            'use_config_qty_increments' => 1,
            'qty_increments' => '0.0000',
            'use_config_enable_qty_inc' => 1,
            'enable_qty_increments' => 0,
            'is_decimal_divided' => 0
        ];
        unset($stockData['item_id']);
        $this->assertEquals($result, $stockData, "The stock data does not match.");
    }
}
