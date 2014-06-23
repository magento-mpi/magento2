<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Model;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class ObserverTest
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManagerHelper
     */
    protected $_objectManagerHelper;

    /**
     * @var \Magento\CatalogInventory\Model\Observer
     */
    protected $_observer;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Price\Processor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_priceIndexer;

    /**
     * @var \Magento\CatalogInventory\Model\Indexer\Stock\Processor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_stockIndexerProcessor;

    /**
     * Set up before test
     */
    public function setUp()
    {
        $this->_objectManagerHelper = new ObjectManagerHelper($this);

        $this->_priceIndexer = $this->getMock(
            '\Magento\Catalog\Model\Indexer\Product\Price\Processor',
            [],
            [],
            '',
            false
        );
        $this->_stockIndexerProcessor = $this->getMock(
            '\Magento\CatalogInventory\Model\Indexer\Stock\Processor',
            [],
            [],
            '',
            false
        );
        $resourceStock = $this->getMock('\Magento\CatalogInventory\Model\Resource\Stock', [], [], '', false);
        $stock = $this->getMock('\Magento\CatalogInventory\Model\Stock', [], [], '', false);
        $stockStatus = $this->getMock('\Magento\CatalogInventory\Model\Stock\Status', [], [], '', false);
        $catalogInventoryData = $this->getMock('\Magento\CatalogInventory\Helper\Data', [], [], '', false);
        $stockItemFactory = $this->getMock('\Magento\CatalogInventory\Model\Stock\ItemFactory', [], [], '', false);
        $stockFactory = $this->getMock('\Magento\CatalogInventory\Model\StockFactory', [], [], '', false);
        $statusFactory = $this->getMock('\Magento\CatalogInventory\Model\Stock\StatusFactory', [], [], '', false);

        $this->_observer = $this->_objectManagerHelper->getObject(
            '\Magento\CatalogInventory\Model\Observer',
            array(
                'priceIndexer' => $this->_priceIndexer,
                'stockIndexerProcessor' => $this->_stockIndexerProcessor,
                'resourceStock' => $resourceStock,
                'stock' => $stock,
                'stockStatus' => $stockStatus,
                'catalogInventoryData' => $catalogInventoryData,
                'stockItemFactory' => $stockItemFactory,
                'stockFactory' => $stockFactory,
                'statusFactory' => $statusFactory,
            )
        );
    }

    public function testRevertQuoteInventoryReindexPriceAndStock()
    {
        $observer = $this->getMock('\Magento\Framework\Event\Observer', ['getEvent'], [], '', false);
        $event = $this->getMock('\Magento\Framework\Event', ['getQuote'], [], '', false);
        $quote = $this->getMock(
            '\Magento\Sales\Model\Quote',
            ['getAllItems', 'setInventoryProcessed', '__wakeup', '__sleep'],
            [],
            '',
            false
        );
        $item = $this->getMock(
            '\Magento\Sales\Model\Quote\Item',
            ['getProductId', 'getChildrenItems', 'getProduct', 'getTotalQty', '__wakeup', '__sleep'],
            [],
            '',
            false
        );
        $product = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false);

        $observer->expects($this->any())
            ->method('getEvent')
            ->will($this->returnValue($event));

        $event->expects($this->any())
            ->method('getQuote')
            ->will($this->returnValue($quote));

        $quote->expects($this->any())
            ->method('getAllItems')
            ->will($this->returnValue([$item]));

        $quote->expects($this->once())
            ->method('setInventoryProcessed')
            ->with(false);

        $item->expects($this->any())
            ->method('getChildrenItems')
            ->will($this->returnValue([]));

        $item->expects($this->any())
            ->method('getProduct')
            ->will($this->returnValue($product));

        $item->expects($this->any())
            ->method('getProductId')
            ->will($this->returnValue(1));

        $item->expects($this->any())
            ->method('getTotalQty')
            ->will($this->returnValue(10));

        $this->_priceIndexer->expects($this->once())
            ->method('reindexList')
            ->with([1]);

        $this->_stockIndexerProcessor->expects($this->once())
            ->method('reindexList')
            ->with([1]);

        $this->_observer->revertQuoteInventory($observer);
    }

    public function testRefundOrderInventoryReindexPriceAndStock()
    {
        $observer = $this->getMock('\Magento\Framework\Event\Observer', ['getEvent'], [], '', false);
        $event = $this->getMock('\Magento\Framework\Event', ['getCreditmemo'], [], '', false);
        $creditmemo = $this->getMock(
            '\Magento\Sales\Model\Order\Creditmemo',
            ['getAllItems', 'getItemByOrderId', '__wakeup', '__sleep'],
            [],
            '',
            false
        );

        $item = $this->getMock(
            '\Magento\Sales\Model\Order\Creditmemo\Item',
            ['getProductId', 'hasBackToStock', 'getBackToStock', 'getQty', 'getOrderItem', '__wakeup', '__sleep'],
            [],
            '',
            false
        );
        $orderItem = $this->getMock(
            '\Magento\Sales\Model\Order\Item',
            ['__wakeup', '__sleep', 'getParentItemId'],
            [],
            '',
            false
        );

        $observer->expects($this->any())
            ->method('getEvent')
            ->will($this->returnValue($event));

        $event->expects($this->any())
            ->method('getCreditmemo')
            ->will($this->returnValue($creditmemo));

        $creditmemo->expects($this->any())
            ->method('getAllItems')
            ->will($this->returnValue([$item]));

        $orderItem->expects($this->any())
            ->method('getParentId')
            ->will($this->returnValue(null));

        $item->expects($this->any())
            ->method('getOrderItem')
            ->will($this->returnValue($orderItem));

        $item->expects($this->any())
            ->method('getProductId')
            ->will($this->returnValue(1));

        $item->expects($this->once())
            ->method('hasBackToStock')
            ->will($this->returnValue(true));

        $item->expects($this->once())
            ->method('getBackToStock')
            ->will($this->returnValue(1));

        $item->expects($this->any())
            ->method('getQty')
            ->will($this->returnValue(1));

        $this->_priceIndexer->expects($this->once())
            ->method('reindexList')
            ->with([1]);

        $this->_stockIndexerProcessor->expects($this->once())
            ->method('reindexList')
            ->with([1]);

        $this->_observer->refundOrderInventory($observer);
    }
}
