<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Api;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Class StockItemTest
 */
class StockItemTest extends WebapiAbstract
{
    /**
     * Service name
     */
    const SERVICE_NAME = 'catalogInventoryStockItemApi';

    /**
     * Service version
     */
    const SERVICE_VERSION = 'V1';

    /**
     * Resource path
     */
    const RESOURCE_PATH = '/V1/stockItem';

    /** @var \Magento\Catalog\Model\Resource\Product\Collection */
    protected $productCollection;

    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $objectManager;

    /**
     * Execute per test initialization
     */
    public function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->productCollection = $this->objectManager->get('Magento\Catalog\Model\Resource\Product\Collection');
    }

    /**
     * Execute per test cleanup
     */
    public function tearDown()
    {
        $this->productCollection->addFieldToFilter('entity_id', array('in' => array(10, 11, 12)))->delete();
        unset($this->productCollection);
    }

    /**
     * @param array $result
     * @return array
     */
    protected function getStockItemBySku($result)
    {
        $productSku = 'simple1';
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/$productSku",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => 'catalogInventoryStockRegistryV1',
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'catalogInventoryStockRegistryV1GetStockItemBySku'
            ]
        ];
        $arguments = ['productSku' => $productSku];
        $apiResult = $this->_webApiCall($serviceInfo, $arguments);
        $result['id'] = $apiResult['id'];
        $this->assertEquals($result, $apiResult, 'The stock data does not match.');
        return $apiResult;
    }

    /**
     * @param array $newData
     * @param array $expectedResult
     * @param array $fixtureData
     * @magentoApiDataFixture Magento/Catalog/_files/multiple_products.php
     * @dataProvider saveStockItemBySkuWithWrongInputDataProvider
     */
    public function testStockItemPUTWithWrongInput($newData, $expectedResult, $fixtureData)
    {
        $stockItemOld = $this->getStockItemBySku($fixtureData);
        $productSku = 'simple1';
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/$productSku",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => 'catalogInventoryStockRegistryV1',
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'catalogInventoryStockRegistryV1UpdateStockItemBySku'
            ]
        ];

        $stockItemDetailsDo = $this->objectManager->get(
            'Magento\CatalogInventory\Api\Data\StockItemInterfaceBuilder'
        )->populateWithArray($newData)->create();
        $arguments = ['productSku' => $productSku, 'stockItem' => $stockItemDetailsDo->getData()];
        $this->assertEquals($stockItemOld['id'], $this->_webApiCall($serviceInfo, $arguments));

        $stockItemFactory = $this->objectManager->get('Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory');
        $stockItem = $stockItemFactory->create();
        $stockItemResource = $this->objectManager->get('Magento\CatalogInventory\Model\Resource\Stock\Item');
        $stockItemResource->loadByProductId($stockItem, $stockItemOld['product_id'], $stockItemOld['website_id']);
        $expectedResult['item_id'] = $stockItem->getId();
        $this->assertEquals($expectedResult, $stockItem->getData());
    }

    /**
     * @return array
     */
    public function saveStockItemBySkuWithWrongInputDataProvider()
    {
        return [
            [
                [
                    'item_id' => 222,
                    'product_id' => 222,
                    'stock_id' => 1,
                    'website_id' => 1,
                    'qty' => '111.0000',
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
                    'notify_stock_qty' => NULL,
                    'use_config_notify_stock_qty' => 1,
                    'manage_stock' => 0,
                    'use_config_manage_stock' => 1,
                    'stock_status_changed_auto' => 0,
                    'use_config_qty_increments' => 1,
                    'qty_increments' => '0.0000',
                    'use_config_enable_qty_inc' => 1,
                    'enable_qty_increments' => 0,
                    'is_decimal_divided' => 0,
                    'show_default_notification_message' => false
                ],
                [
                    'item_id' => '1',
                    'product_id' => '10',
                    'stock_id' => '1',
                    'qty' => '111.0000',
                    'min_qty' => '0.0000',
                    'use_config_min_qty' => '1',
                    'is_qty_decimal' => '0',
                    'backorders' => '0',
                    'use_config_backorders' => '1',
                    'min_sale_qty' => '1.0000',
                    'use_config_min_sale_qty' => '1',
                    'max_sale_qty' => '0.0000',
                    'use_config_max_sale_qty' => '1',
                    'is_in_stock' => '1',
                    'low_stock_date' => NULL,
                    'notify_stock_qty' => NULL,
                    'use_config_notify_stock_qty' => '1',
                    'manage_stock' => '0',
                    'use_config_manage_stock' => '1',
                    'stock_status_changed_auto' => '0',
                    'use_config_qty_increments' => '1',
                    'qty_increments' => '0.0000',
                    'use_config_enable_qty_inc' => '1',
                    'enable_qty_increments' => '0',
                    'is_decimal_divided' => '0',
                    'website_id' => '1',
                    'type_id' => 'simple',
                ],
                [
                    'id' => 1,
                    'product_id' => 10,
                    'website_id' => 1,
                    'stock_id' => 1,
                    'qty' => 100,
                    'is_in_stock' => 1,
                    'is_qty_decimal' => '',
                    'show_default_notification_message' => '',
                    'use_config_min_qty' => 1,
                    'min_qty' => 0,
                    'use_config_min_sale_qty' => 1,
                    'min_sale_qty' => 1,
                    'use_config_max_sale_qty' => 1,
                    'max_sale_qty' => 10000,
                    'use_config_backorders' => 1,
                    'backorders' => 0,
                    'use_config_notify_stock_qty' => 1,
                    'notify_stock_qty' => 1,
                    'use_config_qty_increments' => 1,
                    'qty_increments' => 0,
                    'use_config_enable_qty_inc' => 1,
                    'enable_qty_increments' => '',
                    'use_config_manage_stock' => 1,
                    'manage_stock' => 1,
                    'low_stock_date' => 0,
                    'is_decimal_divided' => '',
                    'stock_status_changed_auto' => 0
                ]
            ],
        ];
    }
}
