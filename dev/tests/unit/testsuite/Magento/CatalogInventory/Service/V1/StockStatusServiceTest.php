<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Service\V1;

/**
 * Test for Magento\CatalogInventory\Service\V1\StockStatusService
 */
class StockStatusServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StockStatusService
     */
    protected $model;

    /**
     * @var \Magento\CatalogInventory\Model\Stock\Status|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stockStatus;

    /**
     * @var \Magento\Catalog\Service\V1\Product\ProductLoader|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productLoader;

    /**
     * @var \Magento\Store\Model\Resolver\Website|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeResolver;

    /**
     * @var Data\StockStatusBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stockStatusBuilder;

    /**
     * @var \Magento\CatalogInventory\Service\V1\StockItemService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stockItemService;

    protected function setUp()
    {
        $this->stockStatus = $this->getMockBuilder('Magento\CatalogInventory\Model\Stock\Status')
            ->disableOriginalConstructor()
            ->getMock();

        $this->productLoader = $this->getMockBuilder('Magento\Catalog\Service\V1\Product\ProductLoader')
            ->disableOriginalConstructor()
            ->getMock();

        $this->scopeResolver = $this->getMockBuilder('Magento\Store\Model\Resolver\Website')
            ->disableOriginalConstructor()
            ->getMock();

        $this->stockStatusBuilder = $this->getMockBuilder('Magento\CatalogInventory\Service\V1\Data\StockStatusBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->stockItemService = $this->getMockBuilder('Magento\CatalogInventory\Service\V1\StockItemService')
            ->disableOriginalConstructor()
            ->getMock();

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectManagerHelper->getObject(
            'Magento\CatalogInventory\Service\V1\StockStatusService',
            [
                'stockStatus' => $this->stockStatus,
                'productLoader' => $this->productLoader,
                'scopeResolver' => $this->scopeResolver,
                'stockStatusBuilder' => $this->stockStatusBuilder,
                'stockItemService' => $this->stockItemService,
            ]
        );
    }

    /**
     * @param int $productId
     * @param int $websiteId
     * @param int $stockId
     * @param mixed $expectedResult
     * @dataProvider getProductStockStatusDataProvider
     */
    public function testGetProductStockStatus($productId, $websiteId, $stockId, $expectedResult)
    {
        $this->stockStatus->expects($this->once())
            ->method('getProductStockStatus')
            ->with([$productId], $websiteId, $stockId)
            ->will($this->returnValue([$productId => 'expected_result']));

        $result = $this->model->getProductStockStatus($productId, $websiteId, $stockId);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function getProductStockStatusDataProvider()
    {
        $productId = 1;
        return [
            [$productId, 3, 4, 'expected_result'],
        ];
    }

    public function testAssignProduct()
    {
        $product = $this->getMockBuilder('Magento\Catalog\Model\Product')->disableOriginalConstructor()->getMock();
        $stockId = 1;
        $stockStatus = false;

        $this->stockStatus->expects($this->once())
            ->method('assignProduct')
            ->with($product, $stockId, $stockStatus)
            ->will($this->returnSelf());

        $this->assertEquals($this->model, $this->model->assignProduct($product, $stockId, $stockStatus));
    }

    /**
     * @param string $productSku
     * @param int $productId
     * @param int $websiteId
     * @param array $productStockStatusArray
     * @param int $stockQty
     * @param array $array
     * @dataProvider getProductStockStatusBySkuDataProvider
     */
    public function testGetProductStockStatusBySku(
        $productSku,
        $productId,
        $websiteId,
        $productStockStatusArray,
        $stockQty,
        $array
    ) {
        // 1. Create mocks
        /** @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject $product */
        $product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Magento\Framework\App\ScopeInterface|\PHPUnit_Framework_MockObject_MockObject $scope */
        $scope = $this->getMockBuilder('Magento\Framework\App\ScopeInterface')
            ->disableOriginalConstructor()
            ->getMock();

        /**
         * @var \Magento\CatalogInventory\Service\V1\Data\StockStatus|\PHPUnit_Framework_MockObject_MockObject $scope
         */
        $stockStatusDataObject = $this->getMockBuilder('Magento\CatalogInventory\Service\V1\Data\StockStatus')
            ->disableOriginalConstructor()
            ->getMock();

        // 2. Set fixtures
        $this->productLoader->expects($this->any())->method('load')->will($this->returnValueMap([
            [$productSku, $product]
        ]));
        $product->expects($this->any())->method('getId')->will($this->returnValue($productId));
        $this->scopeResolver->expects($this->any())->method('getScope')->will($this->returnValue($scope));
        $scope->expects($this->any())->method('getId')->will($this->returnValue($websiteId));
        $this->stockStatusBuilder->expects($this->any())
            ->method('create')
            ->will($this->returnValue($stockStatusDataObject));

        // 3. Set expectations
        $this->stockStatus->expects($this->any())
            ->method('getProductStockStatus')
            ->with([$productId], $websiteId)
            ->will($this->returnValue($productStockStatusArray));

        $this->stockItemService->expects($this->any())
            ->method('getStockQty')
            ->will($this->returnValueMap([[$productId, $stockQty]]));

        $this->stockStatusBuilder->expects($this->any())->method('populateWithArray')->with($array);

        // 4. Run tested method
        $result = $this->model->getProductStockStatusBySku($productSku);

        // 5. Compare actual result with expected result
        $this->assertEquals($stockStatusDataObject, $result);
    }

    /**
     * @return array
     */
    public function getProductStockStatusBySkuDataProvider()
    {
        $productId = 123;

        $productStatusInStock = true;
        $fullStockQty = 456;

        $productStatusOutOfStock = false;
        $emptyStockQty = 0;
        return [
            [
                'sku1',
                $productId,
                1,
                [$productId => $productStatusInStock],
                $fullStockQty,
                [
                    Data\StockStatus::STOCK_STATUS => $productStatusInStock,
                    Data\StockStatus::STOCK_QTY => $fullStockQty
                ]
            ],
            [
                'sku1',
                $productId,
                1,
                [$productId => $productStatusOutOfStock],
                $emptyStockQty,
                [
                    Data\StockStatus::STOCK_STATUS => $productStatusOutOfStock,
                    Data\StockStatus::STOCK_QTY => $emptyStockQty
                ]
            ],
        ];
    }

    /**
     * @param string $productSku
     * @param int $productId
     * @dataProvider getProductStockWithExceptionStatusBySkuDataProvider
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testGetProductStockWithExceptionStatusBySku($productSku, $productId)
    {
        // 1. Create mocks
        /** @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject $product */
        $product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();

        // 2. Set fixtures
        $this->productLoader->expects($this->any())->method('load')->will($this->returnValueMap([
            [$productSku, $product]
        ]));
        $product->expects($this->any())->method('getId')->will($this->returnValue($productId));

        // 3. Run tested method
        $this->model->getProductStockStatusBySku($productSku);
    }

    /**
     * @return array
     */
    public function getProductStockWithExceptionStatusBySkuDataProvider()
    {
        return [
            ['sku1', null],
            ['sku1', false],
            ['sku1', 0],
        ];
    }
}
