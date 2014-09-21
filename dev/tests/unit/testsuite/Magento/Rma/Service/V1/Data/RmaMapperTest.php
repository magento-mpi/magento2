<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Service\V1\Data;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class RmaMapperTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Rma\Service\V1\Data\RmaMapper */
    protected $rmaMapper;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $rmaBuilderMock;

    /** @var \Magento\Rma\Service\V1\Data\ItemMapper|\PHPUnit_Framework_MockObject_MockObject */
    protected $itemMapperMock;

    /** @var \Magento\Rma\Service\V1\TrackReadInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $trackReadMock;

    /** @var \Magento\Rma\Service\V1\CommentReadInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $commentReadMock;

    protected function setUp()
    {
        $this->rmaBuilderMock = $this->getMock(
            'Magento\Rma\Service\V1\Data\RmaBuilder',
            ['populateWithArray', 'setItems', 'setComments', 'setTracks', 'create']
        );
        $this->itemMapperMock = $this->getMock('Magento\Rma\Service\V1\Data\ItemMapper', [], [], '', false);
        $this->trackReadMock = $this->getMock(
            'Magento\Rma\Service\V1\TrackReadInterface',
            ['getTracks', 'getShippingLabelPdf'],
            [],
            '',
            false
        );
        $this->commentReadMock = $this->getMock(
            'Magento\Rma\Service\V1\CommentReadInterface',
            ['commentsList']
        );

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->rmaMapper = $this->objectManagerHelper->getObject(
            'Magento\Rma\Service\V1\Data\RmaMapper',
            [
                'rmaBuilder' => $this->rmaBuilderMock,
                'itemMapper' => $this->itemMapperMock,
                'trackReadService' => $this->trackReadMock,
                'commentReadService' => $this->commentReadMock
            ]
        );
    }

    public function testGetMappedItems()
    {
        $itemDto = $this->getMockBuilder('Magento\Rma\Service\V1\Data\Item')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $expectedResult = [$itemDto];
        $itemCollection = $this->getPreparedItemCollection($itemDto);
        $this->assertSame($expectedResult, $this->rmaMapper->getMappedItems($itemCollection));
    }

    public function testGetMappedComments()
    {
        $rmaId = 1;
        $items = [];

        $this->prepareComments($rmaId, $items);

        $this->assertSame($items, $this->rmaMapper->getMappedComments($rmaId));
    }

    public function testExtractDto()
    {
        $data = [];
        $rmaId = 1;
        $comments = [];
        $itemDto = $this->getMockBuilder('Magento\Rma\Service\V1\Data\Item')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $expectedItems = [$itemDto];
        $expectedTracks = [];
        $rmaMock = $this->getMockBuilder('Magento\Rma\Model\Rma')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $rmaDto = $this->getMockBuilder('Magento\Rma\Service\V1\Data\Rma')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $rmaMock->expects($this->once())->method('getData')
            ->will($this->returnValue($data));
        $rmaMock->expects($this->any())->method('getId')
            ->will($this->returnValue($rmaId));
        $this->rmaBuilderMock->expects($this->once())->method('populateWithArray')
            ->with($data);
        $rmaMock->expects($this->once())->method('getItemsForDisplay')
            ->will($this->returnValue($this->getPreparedItemCollection($itemDto)));
        $this->rmaBuilderMock->expects($this->once())->method('setItems')
            ->with($expectedItems);
        $this->prepareComments($rmaId, $comments);
        $this->rmaBuilderMock->expects($this->once())->method('setComments')
            ->with($comments);
        $this->trackReadMock->expects($this->once())->method('getTracks')
            ->with($rmaId)
            ->will($this->returnValue($expectedTracks));
        $this->rmaBuilderMock->expects($this->once())->method('setTracks')
            ->will($this->returnValue($expectedTracks));
        $this->rmaBuilderMock->expects($this->once())->method('create')
            ->will($this->returnValue($rmaDto));

        $this->assertSame($rmaDto, $this->rmaMapper->extractDto($rmaMock));
    }

    /**
     * @param $itemDto
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getPreparedItemCollection($itemDto)
    {
        $itemCollection = $this->getMockBuilder('Magento\Rma\Model\Resource\Item\Collection')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $itemMock = $this->getMockBuilder('Magento\Rma\Model\Item')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $itemCollection->expects($this->once())->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator([$itemMock])));
        $this->itemMapperMock->expects($this->once())->method('extractDto')
            ->with($itemMock)
            ->will($this->returnValue($itemDto));

        return $itemCollection;
    }

    /**
     * @param $rmaId
     * @param $items
     */
    private function prepareComments($rmaId, $items)
    {
        $commentsResultMock = $this->getMockBuilder('Magento\Rma\Service\V1\Data\RmaStatusHistorySearchResults')
            ->disableOriginalConstructor()
            ->setMethods(['getItems'])
            ->getMock();

        $this->commentReadMock->expects($this->once())->method('commentsList')
            ->with($rmaId)
            ->will($this->returnValue($commentsResultMock));
        $commentsResultMock->expects($this->once())->method('getItems')
            ->will($this->returnValue($items));
    }
}
