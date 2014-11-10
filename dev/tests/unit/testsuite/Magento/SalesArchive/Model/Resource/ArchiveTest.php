<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesArchive\Model\Resource;

/**
 * Tests for resource Archive
 *
 * Class ArchiveTest
 */
class ArchiveTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\SalesArchive\Model\Archive|
     */
    protected $archive;

    /**
     * @var \Magento\SalesArchive\Model\Archive|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $archiveMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject ///\Magento\SalesArchive\Model\Resource\Archive|
     */
    protected $resourceArchiveMock;

    /**
     * @var \Magento\Framework\App\Resource|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceMock;

    /**
     * @var \Magento\SalesArchive\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Magento\SalesArchive\Model\ArchivalList|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $archivalListMock;

    /**
     * @var \Magento\Framework\Stdlib\DateTime|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dateTimeMock;


    public function setUp()
    {
        $this->resourceMock = $this->getMock(
            'Magento\Framework\App\Resource',
            [],
            [],
            '',
            false,
            false
        );

        $this->configMock = $this->getMock(
            'Magento\SalesArchive\Model\Config',
            [],
            [],
            '',
            false,
            false
        );

        $this->archivalListMock = $this->getMock(
            'Magento\SalesArchive\Model\ArchivalList',
            [],
            [],
            '',
            false,
            false
        );

        $this->dateTimeMock = $this->getMock(
            'Magento\Framework\Stdlib\DateTime',
            [],
            [],
            '',
            false,
            false
        );

        $this->resourceArchiveMock = $this->getMockBuilder('Magento\SalesArchive\Model\Resource\Archive')
            ->setConstructorArgs([
                $this->resourceMock,
                $this->configMock,
                $this->archivalListMock,
                $this->dateTimeMock
            ])
            ->setMethods([
                'getIdsInArchive',
                'beginTransaction',
                'removeFromArchive',
                'commit',
                'rollback'
            ])
            ->getMock();

        $this->archive = new \Magento\SalesArchive\Model\Resource\Archive(
            $this->resourceMock,
            $this->configMock,
            $this->archivalListMock,
            $this->dateTimeMock
        );
    }

    public function testRemoveOrdersFromArchiveById()
    {
        $ids = [100021, 100023, 100054];
        $entity = 'entity_id';
        $order = 'order_id';

        $this->resourceArchiveMock->expects($this->once())
            ->method('getIdsInArchive')
            ->with($this->equalTo(\Magento\SalesArchive\Model\ArchivalList::ORDER), $this->equalTo($ids))
            ->will($this->returnValue($ids));
        $this->resourceArchiveMock->expects($this->once())
            ->method('beginTransaction')
            ->will($this->returnSelf());

        $this->resourceArchiveMock->expects($this->at(2))
            ->method('removeFromArchive')
            ->with($this->equalTo(\Magento\SalesArchive\Model\ArchivalList::ORDER), $entity, $this->equalTo($ids))
            ->will($this->returnSelf());
        $this->resourceArchiveMock->expects($this->at(3))
            ->method('removeFromArchive')
            ->with($this->equalTo(\Magento\SalesArchive\Model\ArchivalList::INVOICE), $order, $this->equalTo($ids))
            ->will($this->returnSelf());
        $this->resourceArchiveMock->expects($this->at(4))
            ->method('removeFromArchive')
            ->with($this->equalTo(\Magento\SalesArchive\Model\ArchivalList::SHIPMENT), $order, $this->equalTo($ids))
            ->will($this->returnSelf());
        $this->resourceArchiveMock->expects($this->at(5))
            ->method('removeFromArchive')
            ->with($this->equalTo(\Magento\SalesArchive\Model\ArchivalList::CREDITMEMO), $order, $this->equalTo($ids))
            ->will($this->returnSelf());
        $this->resourceArchiveMock->expects($this->at(6))
            ->method('commit')
            ->will($this->returnSelf());
        $result = $this->resourceArchiveMock->removeOrdersFromArchiveById($ids);
        $this->assertEquals($ids, $result);
    }

    /**
     * @expectedException \Exception
     */
    public function testRemoveOrdersFromArchiveByIdException()
    {
        $ids = [100021, 100023, 100054];
        $entity = 'entity_id';

        $this->resourceArchiveMock->expects($this->once())
            ->method('getIdsInArchive')
            ->with($this->equalTo(\Magento\SalesArchive\Model\ArchivalList::ORDER), $this->equalTo($ids))
            ->will($this->returnValue($ids));
        $this->resourceArchiveMock->expects($this->once())
            ->method('beginTransaction')
            ->will($this->returnSelf());
        $this->resourceArchiveMock->expects($this->once())
            ->method('removeFromArchive')
            ->with($this->equalTo(\Magento\SalesArchive\Model\ArchivalList::ORDER), $entity, $this->equalTo($ids))
            ->will($this->throwException(new \Exception()));
        $this->resourceArchiveMock->expects($this->once())
            ->method('rollback');

        $result = $this->resourceArchiveMock->removeOrdersFromArchiveById($ids);
        $this->assertInstanceOf('Exception', $result);
    }
}
