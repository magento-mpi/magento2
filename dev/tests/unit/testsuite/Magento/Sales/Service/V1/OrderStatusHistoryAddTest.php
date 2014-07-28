<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\Order\Status\HistoryConverter;


/**
 * Class OrderGetTest
 * @package Magento\Sales\Service\V1
 */
class OrderGetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Service\V1\OrderStatusHistoryAdd
     */
    protected $service;

    /**
     * @var OrderRepository | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderRepositoryMock;

    /**
     * @var HistoryConverter | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $historyConverterMock;

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
        $this->historyConverterMock = $this->getMock(
            'Magento\Sales\Model\Order\Status\HistoryConverter',
            ['getModel'],
            [],
            '',
            false
        );
        $this->service = new OrderStatusHistoryAdd(
            $this->orderRepositoryMock,
            $this->historyConverterMock
        );
    }

    /**
     * test order list service
     */
    public function testInvoke()
    {
        $id = 1;

        $dataObject = $this->getMock('Magento\Sales\Service\V1\Data\OrderStatusHistory', [], [], '', false);
        $model = $this->getMock('Magento\Sales\Model\Order\Status\History', [], [], '', false);
        $this->historyConverterMock->expects($this->once())
            ->method('getModel')
            ->with($dataObject)
            ->will($this->returnValue($model));
        $orderMock = $this->getMock('Magento\Sales\Model\Order', [], [], '', false);
        $orderMock->expects($this->once())
            ->method('addStatusHistory')
            ->with($model);
        $this->orderRepositoryMock->expects($this->once())
            ->method('get')
            ->with($id)
            ->will($this->returnValue($orderMock));

        $this->service->invoke($id, $dataObject);
    }
}
