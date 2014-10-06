<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

/**
 * Class OrderCancelTest
 */
class OrderCancelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Service\V1\Action\OrderCancel
     */
    protected $orderCancel;

    /**
     * @var \Magento\Sales\Model\OrderRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderRepositoryMock;

    /**
     * @var \Magento\Sales\Model\Order|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderMock;

    /**
     * SetUp
     */
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
        $this->orderCancel = new OrderCancel(
            $this->orderRepositoryMock
        );
    }

    /**
     * test order cancel service
     */
    public function testInvoke()
    {
        $this->orderRepositoryMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo(1))
            ->will($this->returnValue($this->orderMock));
        $this->orderMock->expects($this->once())
            ->method('cancel')
            ->will($this->returnSelf());
        $this->assertTrue($this->orderCancel->invoke(1));
    }
}
