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
    /**
     * Service name
     */
    const SERVICE_NAME = 'catalogInventoryStockItemServiceV1';

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

    /** @var \Magento\Framework\ObjectManager */
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
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'catalogInventoryStockItemServiceV1GetStockItemBySku'
            ]
        ];
        $arguments = ['productSku' => $productSku];
        $apiResult = $this->_webApiCall($serviceInfo, $arguments);
        $result['item_id'] = $apiResult['item_id'];
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
    public function testSaveStockItemBySkuWithWrongInput($newData, $expectedResult, $fixtureData)
    {
        $stockItemOld = $this->getStockItemBySku($fixtureData);
        $productSku = 'simple1';
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/$productSku",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'catalogInventoryStockItemServiceV1SaveStockItemBySku'
            ]
        ];

        $stockItemDetailsDo = $this->objectManager->get(
            'Magento\CatalogInventory\Service\V1\Data\StockItemDetailsBuilder'
        )->populateWithArray($newData)->create();

        $arguments = ['productSku' => $productSku, 'stockItemDetailsDo' => $stockItemDetailsDo->__toArray()];
        $this->assertEquals($stockItemOld['item_id'], $this->_webApiCall($serviceInfo, $arguments));
        $stockItemFactory = $this->objectManager->get('Magento\CatalogInventory\Model\Stock\ItemFactory');

        $stockItem = $stockItemFactory->create();
        $stockItemResource = $this->objectManager->get('Magento\CatalogInventory\Model\Resource\Stock\Item');
        $stockItemResource->loadByProductId($stockItem, $stockItemOld['product_id']);
        $expectedResult['item_id'] = $stockItem->getItemId();
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
                    'stock_id' => 2,
                    'qty' => '111.0000',
                    'min_qty' => '2.0000',
                    'use_config_min_qty' => 2,
                    'is_qty_decimal' => 1,
                    'backorders' => 1,
                    'use_config_backorders' => 0,
                    'min_sale_qty' => '2.0000',
                    'use_config_min_sale_qty' => 0,
                    'max_sale_qty' => '100.0000',
                    'use_config_max_sale_qty' => 0,
                    'is_in_stock' => 0,
                    'low_stock_date' => '',
                    'notify_stock_qty' => '11',
                    'use_config_notify_stock_qty' => 0,
                    'manage_stock' => 1,
                    'use_config_manage_stock' => 0,
                    'stock_status_changed_auto' => 1,
                    'use_config_qty_increments' => 0,
                    'qty_increments' => '1.0000',
                    'use_config_enable_qty_inc' => 0,
                    'enable_qty_increments' => 1,
                    'is_decimal_divided' => 1
                ],
                [
                    'stock_id' => 1,
                    'product_id' => 10,
                    'qty' => '111.0000',
                    'min_qty' => '2.0000',
                    'use_config_min_qty' => 1,
                    'is_qty_decimal' => 1,
                    'backorders' => 1,
                    'use_config_backorders' => 1,
                    'min_sale_qty' => '2.0000',
                    'use_config_min_sale_qty' => 1,
                    'max_sale_qty' => '100.0000',
                    'use_config_max_sale_qty' => 1,
                    'is_in_stock' => 0,
                    'low_stock_date' => '',
                    'notify_stock_qty' => '11.0000',
                    'use_config_notify_stock_qty' => 1,
                    'manage_stock' => 1,
                    'use_config_manage_stock' => 1,
                    'stock_status_changed_auto' => 0,
                    'use_config_qty_increments' => 1,
                    'qty_increments' => '1.0000',
                    'use_config_enable_qty_inc' => 1,
                    'enable_qty_increments' => 1,
                    'is_decimal_divided' => 1,
                    'type_id' => 'simple'
                ],
                [
                    'product_id' => 10,
                    'stock_id' => 1,
                    'qty' => 100,
                    'min_qty' => 0,
                    'use_config_min_qty' => true,
                    'is_qty_decimal' => false,
                    'backorders' => false,
                    'use_config_backorders' => true,
                    'min_sale_qty' => 1,
                    'use_config_min_sale_qty' => true,
                    'max_sale_qty' => 0,
                    'use_config_max_sale_qty' => true,
                    'is_in_stock' => true,
                    'use_config_notify_stock_qty' => true,
                    'manage_stock' => false,
                    'use_config_manage_stock' => true,
                    'stock_status_changed_auto' => false,
                    'use_config_qty_increments' => true,
                    'qty_increments' => 0,
                    'use_config_enable_qty_inc' => true,
                    'enable_qty_increments' => false,
                    'is_decimal_divided' => false
                ]
            ],
        ];
    }
}
