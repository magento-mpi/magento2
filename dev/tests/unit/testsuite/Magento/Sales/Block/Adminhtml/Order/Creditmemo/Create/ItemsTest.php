<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Block\Adminhtml\Order\Creditmemo\Create;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class ItemsTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Sales\Block\Adminhtml\Order\Creditmemo\Create\Items */
    protected $items;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Backend\Block\Template\Context|\PHPUnit_Framework_MockObject_MockObject */
    protected $contextMock;

    /** @var \Magento\CatalogInventory\Service\V1\StockItem|\PHPUnit_Framework_MockObject_MockObject */
    protected $stockItemMock;

    /** @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject */
    protected $registryMock;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $scopeConfig;

    protected function setUp()
    {
        $this->contextMock = $this->getMock('Magento\Backend\Block\Template\Context', [], [], '', false);
        $this->stockItemMock = $this->getMock('Magento\CatalogInventory\Service\V1\StockItem', [], [], '', false);
        $this->registryMock = $this->getMock('Magento\Framework\Registry');
        $this->scopeConfig = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');
        $this->contextMock->expects($this->once())
            ->method('getScopeConfig')
            ->will($this->returnValue($this->scopeConfig));

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->items = $this->objectManagerHelper->getObject(
            'Magento\Sales\Block\Adminhtml\Order\Creditmemo\Create\Items',
            [
                'context' => $this->contextMock,
                'stockItemService' => $this->stockItemMock,
                'registry' => $this->registryMock
            ]
        );
    }

    /**
     * @param bool $canReturnToStock
     * @param bool $manageStock
     * @param bool $result
     * @dataProvider canReturnItemsToStockDataProvider
     */
    public function testCanReturnItemsToStock($canReturnToStock, $manageStock, $result)
    {
        $productId = 7;
        $property = new \ReflectionProperty($this->items, '_canReturnToStock');
        $property->setAccessible(true);
        $this->assertNull($property->getValue($this->items));
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(
                $this->equalTo(\Magento\CatalogInventory\Model\Stock\Item::XML_PATH_CAN_SUBTRACT),
                $this->equalTo(\Magento\Store\Model\ScopeInterface::SCOPE_STORE)
            )
            ->will($this->returnValue($canReturnToStock));

        if ($canReturnToStock) {
            $orderItem = $this->getMock('Magento\Sales\Model\Order\Item', ['getProductId', '__wakeup'], [], '', false);
            $orderItem->expects($this->once())
                ->method('getProductId')
                ->will($this->returnValue($productId));

            $creditMemoItem = $this->getMock(
                'Magento\Sales\Model\Order\Creditmemo\Item',
                ['setCanReturnToStock', 'getOrderItem', '__wakeup'],
                [],
                '',
                false
            );

            $creditMemo = $this->getMock('Magento\Sales\Model\Order\Creditmemo', [], [], '', false);
            $creditMemo->expects($this->once())
                ->method('getAllItems')
                ->will($this->returnValue([$creditMemoItem]));
            $creditMemoItem->expects($this->once())
                ->method('getOrderItem')
                ->will($this->returnValue($orderItem));

            $this->stockItemMock->expects($this->once())
                ->method('getManageStock')
                ->with($this->equalTo($productId))
                ->will($this->returnValue($manageStock));

            $creditMemoItem->expects($this->once())
                ->method('setCanReturnToStock')
                ->with($this->equalTo($manageStock))
                ->will($this->returnSelf());

            $order = $this->getMock('Magento\Sales\Model\Order', ['setCanReturnToStock', '__wakeup'], [], '', false);
            $order->expects($this->once())
                ->method('setCanReturnToStock')
                ->with($this->equalTo($manageStock))
                ->will($this->returnSelf());
            $creditMemo->expects($this->once())
                ->method('getOrder')
                ->will($this->returnValue($order));

            $this->registryMock->expects($this->any())
                ->method('registry')
                ->with('current_creditmemo')
                ->will($this->returnValue($creditMemo));
        }

        $this->assertSame($result, $this->items->canReturnItemsToStock());
        $this->assertSame($result, $property->getValue($this->items));
        // lazy load test
        $this->assertSame($result, $this->items->canReturnItemsToStock());
    }

    /**
     * @return array
     */
    public function canReturnItemsToStockDataProvider()
    {
        return [
            'cannot subtract by config' => [false, true, false],
            'manage stock is enabled' => [true, true, true],
            'manage stock is disabled' => [true, false, false],
        ];
    }
}
