<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Model\Quote;

/**
 * Class DiscountTest
 */
class DiscountTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\SalesRule\Model\Quote\Discount
     */
    protected $discount;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $validatorMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManagerMock;

    public function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->storeManagerMock = $this->getMockBuilder('Magento\Store\Model\StoreManager')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->validatorMock = $this->getMockBuilder('Magento\SalesRule\Model\Validator')
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'canApplyRules',
                    'reset',
                    'init',
                    'initTotals',
                    'sortItemsByPriority',
                    'setSkipActionsValidation',
                    'process',
                    'processShippingAmount'
                ]
            )
            ->getMock();
        $this->eventManagerMock = $this->getMockBuilder('Magento\Framework\Event\Manager')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        /** @var \Magento\SalesRule\Model\Quote\Discount $discount */
        $this->discount = $this->objectManager->getObject(
            'Magento\SalesRule\Model\Quote\Discount',
            [
                'storeManager' => $this->storeManagerMock,
                'validator' => $this->validatorMock,
                'eventManager' => $this->eventManagerMock
            ]
        );
    }

    public function testCollectItemNoDiscount()
    {
        $itemNoDiscount = $this->getMockBuilder('Magento\Sales\Model\Quote\Item')
            ->disableOriginalConstructor()
            ->setMethods(['getNoDiscount'])
            ->getMock();
        $itemNoDiscount->expects($this->once())
            ->method('getNoDiscount')
            ->willReturn(true);

        $this->validatorMock->expects($this->any())
            ->method('sortItemsByPriority')
            ->willReturnArgument(0);

        $storeMock = $this->getMockBuilder('Magento\Store\Model\Store')
            ->disableOriginalConstructor()
            ->setMethods(['getStore'])
            ->getMock();
        $this->storeManagerMock->expects($this->any())
            ->method('getStore')
            ->willReturn($storeMock);

        $quoteMock = $this->getMockBuilder('Magento\Sales\Model\Quote')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $addressMock = $this->getMockBuilder('Magento\Sales\Model\Quote\Address')
            ->disableOriginalConstructor()
            ->setMethods(['getQuote', 'getAllNonNominalItems', 'getShippingAmount'])
            ->getMock();
        $addressMock->expects($this->any())
            ->method('getQuote')
            ->willReturn($quoteMock);
        $addressMock->expects($this->any())
            ->method('getAllNonNominalItems')
            ->willReturn([$itemNoDiscount]);
        $addressMock->expects($this->any())
            ->method('getShippingAmount')
            ->willReturn(true);

        $this->assertInstanceOf(
            'Magento\SalesRule\Model\Quote\Discount',
            $this->discount->collect($addressMock)
        );
    }

    public function testCollectItemHasParent()
    {
        $itemWithParentId = $this->getMockBuilder('Magento\Sales\Model\Quote\Item')
            ->disableOriginalConstructor()
            ->setMethods(['getNoDiscount', 'getParentItemId'])
            ->getMock();
        $itemWithParentId->expects($this->once())
            ->method('getNoDiscount')
            ->willReturn(false);
        $itemWithParentId->expects($this->once())
            ->method('getParentItemId')
            ->willReturn(true);

        $this->validatorMock->expects($this->any())
            ->method('sortItemsByPriority')
            ->willReturnArgument(0);

        $storeMock = $this->getMockBuilder('Magento\Store\Model\Store')
            ->disableOriginalConstructor()
            ->setMethods(['getStore'])
            ->getMock();
        $this->storeManagerMock->expects($this->any())
            ->method('getStore')
            ->willReturn($storeMock);

        $quoteMock = $this->getMockBuilder('Magento\Sales\Model\Quote')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $addressMock = $this->getMockBuilder('Magento\Sales\Model\Quote\Address')
            ->disableOriginalConstructor()
            ->setMethods(['getQuote', 'getAllNonNominalItems', 'getShippingAmount'])
            ->getMock();
        $addressMock->expects($this->any())
            ->method('getQuote')
            ->willReturn($quoteMock);
        $addressMock->expects($this->any())
            ->method('getAllNonNominalItems')
            ->willReturn([$itemWithParentId]);
        $addressMock->expects($this->any())
            ->method('getShippingAmount')
            ->willReturn(true);

        $this->assertInstanceOf(
            'Magento\SalesRule\Model\Quote\Discount',
            $this->discount->collect($addressMock)
        );
    }

    public function testCollectItemHasChildren()
    {
        $child = $this->getMockBuilder('Magento\Sales\Model\Quote\Item')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $child->expects($this->any())
            ->method('getParentItem')
            ->willReturnSelf();
        $child->expects($this->any())
            ->method('getPrice')
            ->willReturn(1);
        $child->expects($this->any())
            ->method('getBaseOriginalPrice')
            ->willReturn(1);

        $itemWithChildren = $this->getMockBuilder('Magento\Sales\Model\Quote\Item')
            ->disableOriginalConstructor()
            ->setMethods(['getNoDiscount', 'getParentItemId', 'getHasChildren', 'isChildrenCalculated', 'getChildren'])
            ->getMock();
        $itemWithChildren->expects($this->once())
            ->method('getNoDiscount')
            ->willReturn(false);
        $itemWithChildren->expects($this->once())
            ->method('getParentItemId')
            ->willReturn(false);
        $itemWithChildren->expects($this->once())
            ->method('getHasChildren')
            ->willReturn(true);
        $itemWithChildren->expects($this->once())
            ->method('isChildrenCalculated')
            ->willReturn(true);
        $itemWithChildren->expects($this->once())
            ->method('getChildren')
            ->willReturn([$child]);

        $this->validatorMock->expects($this->any())
            ->method('sortItemsByPriority')
            ->willReturnArgument(0);
        $this->validatorMock->expects($this->any())
            ->method('canApplyRules')
            ->willReturn(true);

        $storeMock = $this->getMockBuilder('Magento\Store\Model\Store')
            ->disableOriginalConstructor()
            ->setMethods(['getStore'])
            ->getMock();
        $this->storeManagerMock->expects($this->any())
            ->method('getStore')
            ->willReturn($storeMock);

        $quoteMock = $this->getMockBuilder('Magento\Sales\Model\Quote')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $addressMock = $this->getMockBuilder('Magento\Sales\Model\Quote\Address')
            ->disableOriginalConstructor()
            ->setMethods(['getQuote', 'getAllNonNominalItems', 'getShippingAmount'])
            ->getMock();
        $addressMock->expects($this->any())
            ->method('getQuote')
            ->willReturn($quoteMock);
        $addressMock->expects($this->any())
            ->method('getAllNonNominalItems')
            ->willReturn([$itemWithChildren]);
        $addressMock->expects($this->any())
            ->method('getShippingAmount')
            ->willReturn(true);

        $this->assertInstanceOf(
            'Magento\SalesRule\Model\Quote\Discount',
            $this->discount->collect($addressMock)
        );
    }

    public function testCollectItemHasNoChildren()
    {
        $itemWithChildren = $this->getMockBuilder('Magento\Sales\Model\Quote\Item')
            ->disableOriginalConstructor()
            ->setMethods(['getNoDiscount', 'getParentItemId', 'getHasChildren', 'isChildrenCalculated', 'getChildren'])
            ->getMock();
        $itemWithChildren->expects($this->once())
            ->method('getNoDiscount')
            ->willReturn(false);
        $itemWithChildren->expects($this->once())
            ->method('getParentItemId')
            ->willReturn(false);
        $itemWithChildren->expects($this->once())
            ->method('getHasChildren')
            ->willReturn(false);

        $this->validatorMock->expects($this->any())
            ->method('sortItemsByPriority')
            ->willReturnArgument(0);

        $storeMock = $this->getMockBuilder('Magento\Store\Model\Store')
            ->disableOriginalConstructor()
            ->setMethods(['getStore'])
            ->getMock();
        $this->storeManagerMock->expects($this->any())
            ->method('getStore')
            ->willReturn($storeMock);

        $quoteMock = $this->getMockBuilder('Magento\Sales\Model\Quote')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $addressMock = $this->getMockBuilder('Magento\Sales\Model\Quote\Address')
            ->disableOriginalConstructor()
            ->setMethods(['getQuote', 'getAllNonNominalItems', 'getShippingAmount'])
            ->getMock();
        $addressMock->expects($this->any())
            ->method('getQuote')
            ->willReturn($quoteMock);
        $addressMock->expects($this->any())
            ->method('getAllNonNominalItems')
            ->willReturn([$itemWithChildren]);
        $addressMock->expects($this->any())
            ->method('getShippingAmount')
            ->willReturn(true);

        $this->assertInstanceOf(
            'Magento\SalesRule\Model\Quote\Discount',
            $this->discount->collect($addressMock)
        );
    }

    public function testFetch()
    {
        $addressMock = $this->getMockBuilder('Magento\Sales\Model\Quote\Address')
            ->disableOriginalConstructor()
            ->setMethods(['getDiscountAmount', 'getDiscountDescription', 'addTotal'])
            ->getMock();
        $addressMock->expects($this->once())
            ->method('getDiscountAmount')
            ->willReturn(10);
        $addressMock->expects($this->once())
            ->method('getDiscountDescription')
            ->willReturn('test description');

        $this->assertInstanceOf(
            'Magento\SalesRule\Model\Quote\Discount',
            $this->discount->fetch($addressMock)
        );
    }
}
