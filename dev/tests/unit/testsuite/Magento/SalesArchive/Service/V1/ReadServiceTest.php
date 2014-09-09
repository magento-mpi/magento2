<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesArchive\Service\V1;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class ReadServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\SalesArchive\Service\V1\ReadService */
    protected $readService;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Sales\Model\OrderRepository|\PHPUnit_Framework_MockObject_MockObject */
    protected $orderRepositoryMock;

    /** @var \Magento\SalesArchive\Service\V1\Data\ArchiveMapper|\PHPUnit_Framework_MockObject_MockObject */
    protected $archiveMapMock;

    protected function setUp()
    {
        $this->orderRepositoryMock = $this->getMock('Magento\Sales\Model\OrderRepository', ['get'], [], '', false);
        $this->archiveMapMock = $this->getMock('Magento\SalesArchive\Service\V1\Data\ArchiveMapper', [], [], '', false);

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->readService = $this->objectManagerHelper->getObject(
            'Magento\SalesArchive\Service\V1\ReadService',
            [
                'orderRepository' => $this->orderRepositoryMock,
                'archiveMapper' => $this->archiveMapMock
            ]
        );
    }

    public function testGetOrderInfo()
    {
        $id = 9;
        $order = $this->getMock('Magento\Sales\Model\Order', [], [], '', false);
        $archiveData = $this->getMock('Magento\SalesArchive\Service\V1\Data\Archive', [], [], '', false);
        $this->orderRepositoryMock->expects($this->any())
            ->method('get')
            ->with($id)
            ->will($this->returnValue($order));
        $this->archiveMapMock->expects($this->any())
            ->method('extractDto')
            ->with($order)
            ->will($this->returnValue($archiveData));
        $this->assertEquals($archiveData, $this->readService->getOrderInfo($id));
    }
}
