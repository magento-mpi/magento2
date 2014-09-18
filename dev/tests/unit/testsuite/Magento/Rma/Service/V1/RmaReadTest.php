<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Service\V1;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class RmaReadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Rma\Service\V1\RmaRead | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $serviceRmaReadMock;

    /**
     * @var \Magento\Rma\Model\RmaRepository | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaRepositoryMock;

    /**
     * @var \Magento\Rma\Service\V1\Data\RmaMapper | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaMapperMock;

    /**
     * @var \Magento\Rma\Service\V1\Data\RmaSearchResultsBuilder | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaSearchResultsBuilderMock;

    /**
     * @var \Magento\Rma\Model\Rma | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataRmaMock;

    /**
     * @var \Magento\Rma\Service\V1\Data\Rma | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaModelMock;

    /**
     * Sets up the common Mocks.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->rmaRepositoryMock = $this->getMockBuilder('Magento\Rma\Model\RmaRepository')
            ->disableOriginalConstructor()
            ->setMethods(['__wakeup', 'get', 'find'])
            ->getMock();

        $this->rmaMapperMock = $this->getMockBuilder('Magento\Rma\Service\V1\Data\RmaMapper')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->rmaSearchResultsBuilderMock = $this->getMockBuilder(
            'Magento\Rma\Service\V1\Data\RmaSearchResultsBuilder'
        )
            ->disableOriginalConstructor()
            ->setMethods(['setItems', 'setTotalCount', 'setSearchCriteria', 'create'])
            ->getMock();

        $this->rmaModelMock = $this->getMockBuilder('Magento\Rma\Model\Rma')
            ->disableOriginalConstructor()
            ->setMethods(['__wakeup'])
            ->getMock();

        $this->dataRmaMock = $this->getMockBuilder('Magento\Rma\Service\V1\Data\Rma')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->serviceRmaReadMock = (new ObjectManagerHelper($this))->getObject(
            'Magento\Rma\Service\V1\RmaRead',
            [
                "repository" => $this->rmaRepositoryMock,
                "rmaMapper" => $this->rmaMapperMock,
                "rmaSearchResultsBuilder" => $this->rmaSearchResultsBuilderMock,
            ]
        );

    }

    /**
     * Test for get method
     */
    public function testGet()
    {
        $id = 1;

        $this->rmaRepositoryMock->expects($this->once())->method('get')
            ->with($id)
            ->willReturn($this->rmaModelMock);

        $this->rmaMapperMock->expects($this->once())->method('extractDto')
            ->with($this->rmaModelMock)->willReturn($this->dataRmaMock);

        $this->serviceRmaReadMock->get($id);
    }

    /**
     * Test for search method
     */
    public function testSearch()
    {
        $searchCriteriaMock = $this->getMockBuilder('Magento\Framework\Service\V1\Data\SearchCriteria')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $resultsMock = $this->getMockBuilder('Magento\Rma\Service\V1\Data\RmaSearchResults')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->rmaRepositoryMock->expects($this->once())->method('find')
            ->with($searchCriteriaMock)->willReturn([$this->rmaModelMock]);

        $this->rmaMapperMock->expects($this->once())->method('extractDto')
            ->with($this->rmaModelMock)->willReturn($this->dataRmaMock);

        $this->rmaSearchResultsBuilderMock->expects($this->once())->method('setItems')
            ->with([$this->dataRmaMock])->willReturnSelf();

        $this->rmaSearchResultsBuilderMock->expects($this->once())->method('setTotalCount')
            ->with(1)->willReturnSelf();

        $this->rmaSearchResultsBuilderMock->expects($this->once())->method('setSearchCriteria')
            ->with($searchCriteriaMock)->willReturnSelf();

        $this->rmaSearchResultsBuilderMock->expects($this->once())->method('create')
            ->willReturn($resultsMock);

        $this->assertEquals(
            $resultsMock,
            $this->serviceRmaReadMock->search($searchCriteriaMock)
        );
    }
}
