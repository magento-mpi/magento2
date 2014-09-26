<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

use Magento\Sales\Model\OrderRepository;

/**
 * Class OrderGetStatusTest
 * @package Magento\Sales\Service\V1
 */
class OrderGetStatusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OrderGetStatus
     */
    protected $service;

    /**
     * @var OrderRepository | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderRepositoryMock;

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

        $this->service = new OrderGetStatus($this->orderRepositoryMock);
    }

    public function testInvoke()
    {
        $status = 'pending';
        $id = 1;
        $orderMock = $this->getMock(
            'Magento\Sales\Model\Order',
            ['getStatus'],
            [],
            '',
            false
        );
        $orderMock->expects($this->once())
            ->method('getStatus')
            ->will($this->returnValue($status));
        $this->orderRepositoryMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo($id))
            ->will($this->returnValue($orderMock));
        $this->assertEquals($status, $this->service->invoke($id));
    }
}
