<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

/**
 * Class OrderWriteTest
 */
class OrderWriteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Service\V1\Action\OrderAddressUpdate|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderAddressUpdateMock;

    /**
     * @var \Magento\Sales\Service\V1\Action\OrderCancel|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderCancelMock;

    /**
     * @var \Magento\Sales\Service\V1\Action\OrderEmail|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderEmailMock;

    /**
     * @var \Magento\Sales\Service\V1\Action\OrderHold|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderHoldMock;

    /**
     * @var \Magento\Sales\Service\V1\Action\OrderUnHold|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderUnHoldMock;

    /**
     * @var \Magento\Sales\Service\V1\Action\OrderStatusHistoryAdd|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderStatusHistoryAddMock;

    /**
     * @var \Magento\Sales\Service\V1\Action\OrderCreate|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderCreateMock;

    /**
     * @var \Magento\Sales\Service\V1\OrderWrite
     */
    protected $orderWrite;

    /**
     * SetUp
     */
    protected function setUp()
    {
        $this->orderAddressUpdateMock = $this->getMock(
            'Magento\Sales\Service\V1\Action\OrderAddressUpdate',
            ['invoke'],
            [],
            '',
            false
        );
        $this->orderCancelMock = $this->getMock(
            'Magento\Sales\Service\V1\Action\OrderCancel',
            ['invoke'],
            [],
            '',
            false
        );
        $this->orderEmailMock = $this->getMock(
            'Magento\Sales\Service\V1\Action\OrderEmail',
            ['invoke'],
            [],
            '',
            false
        );
        $this->orderHoldMock = $this->getMock(
            'Magento\Sales\Service\V1\Action\OrderHold',
            ['invoke'],
            [],
            '',
            false
        );
        $this->orderUnHoldMock = $this->getMock(
            'Magento\Sales\Service\V1\Action\OrderUnHold',
            ['invoke'],
            [],
            '',
            false
        );
        $this->orderStatusHistoryAddMock = $this->getMock(
            'Magento\Sales\Service\V1\Action\OrderStatusHistoryAdd',
            ['invoke'],
            [],
            '',
            false
        );

        $this->orderCreateMock = $this->getMock(
            'Magento\Sales\Service\V1\Action\OrderCreate',
            ['invoke'],
            [],
            '',
            false
        );

        $this->orderWrite = new OrderWrite(
            $this->orderAddressUpdateMock,
            $this->orderCancelMock,
            $this->orderEmailMock,
            $this->orderHoldMock,
            $this->orderUnHoldMock,
            $this->orderStatusHistoryAddMock,
            $this->orderCreateMock
        );
    }

    /**
     * test order address update
     */
    public function testAddressUpdate()
    {
        $orderAddress = $this->getMock('Magento\Sales\Service\V1\Data\OrderAddress', [], [], '', false);
        $this->orderAddressUpdateMock->expects($this->once())
            ->method('invoke')
            ->with($orderAddress)
            ->will($this->returnValue(true));
        $this->assertTrue($this->orderWrite->addressUpdate($orderAddress));
    }

    /**
     * test order cancel
     */
    public function testCancel()
    {
        $this->orderCancelMock->expects($this->once())
            ->method('invoke')
            ->with(1)
            ->will($this->returnValue(true));
        $this->assertTrue($this->orderWrite->cancel(1));
    }

    /**
     * test order email
     */
    public function testEmail()
    {
        $this->orderEmailMock->expects($this->once())
            ->method('invoke')
            ->with(1)
            ->will($this->returnValue(true));
        $this->assertTrue($this->orderWrite->email(1));
    }

    /**
     * test order hold
     */
    public function testHold()
    {
        $this->orderHoldMock->expects($this->once())
            ->method('invoke')
            ->with(1)
            ->will($this->returnValue(true));
        $this->assertTrue($this->orderWrite->hold(1));
    }

    /**
     * test order unhold
     */
    public function testUnHold()
    {
        $this->orderUnHoldMock->expects($this->once())
            ->method('invoke')
            ->with(1)
            ->will($this->returnValue(true));
        $this->assertTrue($this->orderWrite->unHold(1));
    }

    /**
     * test order status history add
     */
    public function testStatusHistoryAdd()
    {
        $statusHistory = $this->getMock('Magento\Sales\Service\V1\Data\OrderStatusHistory', [], [], '', false);
        $this->orderStatusHistoryAddMock->expects($this->once())
            ->method('invoke')
            ->with(1, $statusHistory)
            ->will($this->returnValue(true));
        $this->assertTrue($this->orderWrite->statusHistoryAdd(1, $statusHistory));
    }

    /**
     * test order create
     */
    public function testCreate()
    {
        $invoiceDataObject = $this->getMock('Magento\Sales\Service\V1\Data\Order', [], [], '', false);
        $this->orderCreateMock->expects($this->once())
            ->method('invoke')
            ->with($invoiceDataObject)
            ->will($this->returnValue(true));
        $this->assertTrue($this->orderWrite->create($invoiceDataObject));
    }
}
