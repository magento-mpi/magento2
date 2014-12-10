<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Rma\Model;

class GridTest extends \PHPUnit_Framework_TestCase
{
    const TEST_STATUS = 'test_pending';

    /**
     * @var \Magento\Rma\Model\Grid
     */
    protected $rmaGrid;

    /**
     * @var \Magento\Rma\Model\Rma\Source\StatusFactory|\PHPUnit_Framework_MockObject
     */
    protected $statusFactoryMock;

    /**
     * @var \Magento\Framework\Model\Context|\PHPUnit_Framework_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject
     */
    protected $registryMock;

    /**
     * @var \Magento\Framework\Model\Resource\AbstractResource|\PHPUnit_Framework_MockObject
     */
    protected $resourceMock;

    /**
     * @var \Magento\Framework\Data\Collection\Db|\PHPUnit_Framework_MockObject
     */
    protected $resourceCollectionMock;

    protected function setUp()
    {
        $this->contextMock = $this->getMock('Magento\Framework\Model\Context', [], [], '', false);
        $this->registryMock = $this->getMock('Magento\Framework\Registry', [], [], '', false);
        $this->statusFactoryMock = $this->getMock(
            'Magento\Rma\Model\Rma\Source\StatusFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->resourceMock = $this->getMockBuilder('Magento\Framework\Model\Resource\AbstractResource')
            ->disableOriginalConstructor()
            ->setMethods(['getIdFieldName'])
            ->getMockForAbstractClass();
        $this->resourceCollectionMock = $this->getMock('Magento\Framework\Data\Collection\Db', [], [], '', false);
        $data = ['status' => static::TEST_STATUS];
        $this->rmaGrid = new \Magento\Rma\Model\Grid(
            $this->contextMock,
            $this->registryMock,
            $this->statusFactoryMock,
            $this->resourceMock,
            $this->resourceCollectionMock,
            $data
        );
    }

    public function testGetStatusLabel()
    {
        $sourceStatus = $this->getMock('Magento\Rma\Model\Rma\Source\Status', ['getItemLabel'], [], '', false);
        $this->statusFactoryMock->expects($this->once())->method('create')->will($this->returnValue($sourceStatus));
        $sourceStatus->expects($this->any())
            ->method('getItemLabel')
            ->willReturn(static::TEST_STATUS);

        $this->assertEquals(static::TEST_STATUS, $this->rmaGrid->getStatusLabel());
    }
}
