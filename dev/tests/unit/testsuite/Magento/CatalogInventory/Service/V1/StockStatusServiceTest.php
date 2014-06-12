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

    protected function setUp()
    {
        $this->stockStatus = $this->getMockBuilder('Magento\CatalogInventory\Model\Stock\Status')
            ->disableOriginalConstructor()
            ->getMock();

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectManagerHelper->getObject(
            'Magento\CatalogInventory\Service\V1\StockStatusService',
            ['stockStatus' => $this->stockStatus]
        );
    }

    /**
     * @param int[] $productIds
     * @param int $websiteId
     * @param int $stockId
     * @param [] $expectedResult
     * @dataProvider getProductStockStatusDataProvider
     */
    public function testGetProductStockStatus($productIds, $websiteId, $stockId, $expectedResult)
    {
        $this->stockStatus->expects($this->once())
            ->method('getProductStockStatus')
            ->with($productIds, $websiteId, $stockId)
            ->will($this->returnValue($expectedResult));

        $result = $this->model->getProductStockStatus($productIds, $websiteId, $stockId);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function getProductStockStatusDataProvider()
    {
        return [
            [[1,2], 3, 4, []],
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
}
