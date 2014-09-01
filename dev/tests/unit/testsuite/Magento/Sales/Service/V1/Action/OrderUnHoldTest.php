<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

/**
 * Class OrderUnHoldTest
 */
class OrderUnHoldTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Service\V1\Action\OrderUnHold
     */
    protected $orderUnHold;

    /**
     * @var \Magento\Sales\Model\OrderRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderRepositoryMock;

    /**
     * @var \Magento\Sales\Model\Order|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderMock;

    protected function setUp()
    {
        $this->orderRepositoryMock = $this->getMock(
            'Magento\Sales\Model\OrderRepository',
            ['get'],
            [],
            '',
            false
        );
        $this->orderMock = $this->getMock(
            'Magento\Sales\Model\Order',
            [],
            [],
            '',
            false
        );
        $this->orderUnHold = new OrderUnHold(
            $this->orderRepositoryMock
        );
    }

    /**
     * test order unhold service
     */
    public function testInvoke()
    {
        $this->orderRepositoryMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo(1))
            ->will($this->returnValue($this->orderMock));
        $this->orderMock->expects($this->once())
            ->method('unhold')
            ->will($this->returnSelf());
        $this->assertTrue($this->orderUnHold->invoke(1));
    }
}
