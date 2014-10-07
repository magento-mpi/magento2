<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Model\Rma;

use Magento\Rma\Service\V1\Data\Rma;
use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Rma\Model\Rma\Converter */
    protected $converter;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $rmaFactoryMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $rmaRepositoryMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $orderRepositoryMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $statusFactoryMock;

    /** @var \Magento\Rma\Model\Rma\RmaDataMapper|\PHPUnit_Framework_MockObject_MockObject */
    protected $rmaDataMapperMock;

    protected function setUp()
    {
        $this->rmaFactoryMock = $this->getMock('Magento\Rma\Model\RmaFactory', ['create'], [], '', false);
        $this->rmaRepositoryMock = $this->getMock('Magento\Rma\Model\RmaRepository', ['get'], [], '', false);
        $this->orderRepositoryMock = $this->getMock('Magento\Sales\Model\OrderRepository', ['get'], [], '', false);
        $this->statusFactoryMock = $this->getMock(
            'Magento\Rma\Model\Rma\Source\StatusFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->rmaDataMapperMock = $this->getMock('Magento\Rma\Model\Rma\RmaDataMapper', [], [], '', false);

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->converter = $this->objectManagerHelper->getObject(
            'Magento\Rma\Model\Rma\Converter',
            [
                'rmaFactory' => $this->rmaFactoryMock,
                'rmaRepository' => $this->rmaRepositoryMock,
                'orderRepository' => $this->orderRepositoryMock,
                'statusFactory' => $this->statusFactoryMock,
                'rmaDataMapper' => $this->rmaDataMapperMock
            ]
        );
    }

    public function testCreateNewRmaModel()
    {
        $rmaDto = $this->getMockBuilder('Magento\Rma\Service\V1\Data\Rma')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $rmaData = ['data'];
        $orderId = 1;
        $filteredData = ['filtered_data'];
        $preparedData = ['preparedData'];
        $orderMock = $this->getMockBuilder('Magento\Sales\Model\Order')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $rmaMock = $this->getMockBuilder('Magento\Rma\Model\Rma')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $rmaDto->expects($this->once())->method('getOrderId')->will($this->returnValue($orderId));
        $this->orderRepositoryMock->expects($this->once())
            ->method('get')
            ->with($orderId)->will($this->returnValue($orderMock));
        $this->rmaDataMapperMock->expects($this->once())->method('filterRmaSaveRequest')
            ->with($rmaData)
            ->will($this->returnValue($filteredData));
        $this->rmaFactoryMock->expects($this->once())->method('create')->will($this->returnValue($rmaMock));
        $this->rmaDataMapperMock->expects($this->once())->method('prepareNewRmaInstanceData')
            ->with($filteredData, $orderMock)
            ->will($this->returnValue($preparedData));
        $rmaMock->expects($this->once())->method('setData')->with($preparedData);

        $this->assertSame($rmaMock, $this->converter->createNewRmaModel($rmaDto, $rmaData));
    }

    public function testGetPreparedModelData()
    {
        $rmaDto = $this->getMockBuilder('Magento\Rma\Service\V1\Data\Rma')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $itemDto = $this->getMockBuilder('Magento\Rma\Service\V1\Data\Item')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $rmaData = ['key' => 'data'];
        $itemData = ['key' => 'data'];
        $itemId = 1;
        $filteredData = $rmaData;
        $filteredData['items'] = [$itemId => $itemData];

        $rmaDto->expects($this->once())->method('__toArray')->will($this->returnValue($rmaData));
        $rmaDto->expects($this->once())->method('getItems')->will($this->returnValue([$itemDto]));
        $itemDto->expects($this->once())->method('getId')->will($this->returnValue($itemId));
        $itemDto->expects($this->once())->method('__toArray')->will($this->returnValue($itemData));
        $this->rmaDataMapperMock->expects($this->once())->method('filterRmaSaveRequest')
            ->with($filteredData)
            ->will($this->returnValue($rmaData));

        $this->assertEquals($rmaData, $this->converter->getPreparedModelData($rmaDto));
    }

    public function testGetModel()
    {
        $rmaId = 1;
        $preparedRmaData = ['items' => []];
        $itemStatuses = ['status'];
        $rmaMock = $this->getMockBuilder('Magento\Rma\Model\Rma')
            ->disableOriginalConstructor()
            ->setMethods(['setStatus', 'setIsUpdate', '__wakeup'])
            ->getMock();
        $sourceStatus = $this->getMockBuilder('Magento\Rma\Model\Rma\Source\Status')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $rmaStatus = 'status';

        $this->rmaRepositoryMock->expects($this->once())->method('get')
            ->with($rmaId)
            ->will($this->returnValue($rmaMock));
        $this->rmaDataMapperMock->expects($this->once())->method('combineItemStatuses')
            ->with($preparedRmaData['items'], $rmaId)
            ->will($this->returnValue($itemStatuses));
        $this->statusFactoryMock->expects($this->once())->method('create')
            ->will($this->returnValue($sourceStatus));
        $sourceStatus->expects($this->once())->method('getStatusByItems')
            ->with($itemStatuses)
            ->will($this->returnValue($rmaStatus));
        $rmaMock->expects($this->once())->method('setStatus')
            ->with($rmaStatus);
        $rmaMock->expects($this->once())->method('setIsUpdate')
            ->with(1);

        $this->assertSame($rmaMock, $this->converter->getModel($rmaId, $preparedRmaData));
    }
}
