<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Service\V1\Data;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class RmaStatusHistoryMapperTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Rma\Service\V1\Data\RmaStatusHistoryMapper */
    protected $rmaStatusHistoryMapper;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Rma\Service\V1\Data\RmaStatusHistoryBuilder|\PHPUnit_Framework_MockObject_MockObject */
    protected $rmaStatusHistoryBuilderMock;

    protected function setUp()
    {
        $this->rmaStatusHistoryBuilderMock = $this->getMock(
            'Magento\Rma\Service\V1\Data\RmaStatusHistoryBuilder',
            [],
            [],
            '',
            false
        );

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->rmaStatusHistoryMapper = $this->objectManagerHelper->getObject(
            'Magento\Rma\Service\V1\Data\RmaStatusHistoryMapper',
            [
                'rmaStatusHistoryBuilder' => $this->rmaStatusHistoryBuilderMock
            ]
        );
    }

    public function testExtractDto()
    {
        list($data, $isAdmin, $isVisibleOnFront, $isCustomerNotified) = [['data'], 1 , 1, 1];
        $historyModel = $this->getMockBuilder('Magento\Rma\Model\Rma\Status\History')
            ->disableOriginalConstructor()
            ->setMethods(['getData', 'getIsAdmin', 'getIsVisibleOnFront', 'getIsCustomerNotified', '__wakeup'])
            ->getMock();
        $historyDto = $this->getMockBuilder('Magento\Rma\Service\V1\Data\RmaStatusHistory')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $historyModel->expects($this->once())->method('getData')
            ->will($this->returnValue($data));
        $this->rmaStatusHistoryBuilderMock->expects($this->once())->method('populateWithArray')
            ->with($data);
        $historyModel->expects($this->once())->method('getIsAdmin')
            ->will($this->returnValue($isAdmin));
        $this->rmaStatusHistoryBuilderMock->expects($this->once())->method('setAdmin')
            ->with($isAdmin);
        $historyModel->expects($this->once())->method('getIsVisibleOnFront')
            ->will($this->returnValue($isVisibleOnFront));
        $this->rmaStatusHistoryBuilderMock->expects($this->once())->method('setVisibleOnFront')
            ->with($isVisibleOnFront);
        $historyModel->expects($this->once())->method('getIsCustomerNotified')
            ->will($this->returnValue($isCustomerNotified));
        $this->rmaStatusHistoryBuilderMock->expects($this->once())->method('setCustomerNotified')
            ->with($isCustomerNotified);
        $this->rmaStatusHistoryBuilderMock->expects($this->once())->method('create')
            ->will($this->returnValue($historyDto));

        $this->assertSame($historyDto, $this->rmaStatusHistoryMapper->extractDto($historyModel));
    }
}
