<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Service\V1;

use Magento\CatalogInventory\Model\Stock\Status;

/**
 * Test for Magento\CatalogInventory\Service\V1\StockStatusService
 */
class StockStatusServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param int[] $productIds
     * @param int $websiteId
     * @param int $stockId
     * @param [] $expectedResult
     * @dataProvider getProductStockStatusDataProvider
     */
    public function testGetProductStockStatus($productIds, $websiteId, $stockId, $expectedResult)
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);

        // 1 Create mocks
        $stockStatus = $this->getMockBuilder('Magento\CatalogInventory\Model\Stock\Status')
            ->disableOriginalConstructor()
            ->getMock();
        $model = new StockStatusService($stockStatus);

        // 2. Set expectations
        $stockStatus->expects($this->once())
            ->method('getProductStockStatus')
            ->with($productIds, $websiteId, $stockId)
            ->will($this->returnValue($expectedResult));

        // 3. Run tested method
        $result = $model->getProductStockStatus($productIds, $websiteId, $stockId);

        // 5. Compare actual result with expected result
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
}
