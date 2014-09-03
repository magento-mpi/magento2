<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

/**
 * Class OrderHoldTest
 */
class OrderHoldTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Service\V1\Action\OrderHold
     */
    protected $orderHold;

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
        $this->orderHold = new OrderHold(
            $this->orderRepositoryMock
        );
    }

    /**
     * test order hold service
     */
    public function testInvoke()
    {
        $this->orderRepositoryMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo(1))
            ->will($this->returnValue($this->orderMock));
        $this->orderMock->expects($this->once())
            ->method('hold')
            ->will($this->returnSelf());
        $this->assertTrue($this->orderHold->invoke(1));
    }
}
