<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftWrapping\Service\V1;

use Magento\GiftWrapping\Model\WrappingRepository;
use Magento\GiftWrapping\Service\V1\Data\WrappingMapper;
use Magento\GiftWrapping\Service\V1\Data\WrappingConverter;

class WrappingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var WrappingRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $wrappingRepositoryMock;

    /**
     * @var WrappingMapper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $wrappingMapperMock;

    /**
     * @var WrappingConverter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $wrappingConverterMock;

    /**
     * @var Data\WrappingSearchResultsBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchResultsBuilderMock;

    /**
     * @var WrappingRead
     */
    private $readService;

    /**
     * @var WrappingWrite
     */
    private $writeService;

    public function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->wrappingRepositoryMock = $this->getMockBuilder('Magento\GiftWrapping\Model\WrappingRepository')
            ->disableOriginalConstructor()->setMethods([])->getMock();
        $this->wrappingMapperMock = $this->getMockBuilder('Magento\GiftWrapping\Service\V1\Data\WrappingMapper')
            ->disableOriginalConstructor()->setMethods([])->getMock();
        $this->wrappingConverterMock = $this->getMockBuilder('Magento\GiftWrapping\Service\V1\Data\WrappingConverter')
            ->disableOriginalConstructor()->setMethods([])->getMock();
        $this->searchResultsBuilderMock = $this->getMockBuilder(
            'Magento\GiftWrapping\Service\V1\Data\WrappingSearchResultsBuilder'
        )->disableOriginalConstructor()->setMethods([])->getMock();

        $this->readService = $objectManager->getObject(
            'Magento\GiftWrapping\Service\V1\WrappingRead',
            [
                'wrappingRepository' => $this->wrappingRepositoryMock,
                'wrappingMapper' => $this->wrappingMapperMock,
                'searchResultsBuilder' => $this->searchResultsBuilderMock
            ]
        );

        $this->writeService = $objectManager->getObject(
            'Magento\GiftWrapping\Service\V1\WrappingWrite',
            [
                'wrappingRepository' => $this->wrappingRepositoryMock,
                'wrappingConverter' => $this->wrappingConverterMock
            ]
        );
    }

    public function testGet()
    {
        $id = 1;

        /** @var \Magento\GiftWrapping\Model\Wrapping|\PHPUnit_Framework_MockObject_MockObject $wrappingModel */
        $wrappingModel = $this->getMockBuilder('Magento\GiftWrapping\Model\Wrapping')->disableOriginalConstructor()
            ->setMethods([])->getMock();
        /** @var Data\Wrapping|\PHPUnit_Framework_MockObject_MockObject $wrappingDto */
        $wrappingDto = $this->getMockBuilder('Magento\GiftWrapping\Service\V1\Data\Wrapping')
            ->disableOriginalConstructor()->setMethods([])->getMock();

        $this->wrappingRepositoryMock->expects($this->once())->method('get')->with($id)
            ->will($this->returnValue($wrappingModel));
        $this->wrappingMapperMock->expects($this->once())->method('extractDto')->with($wrappingModel)
            ->will($this->returnValue($wrappingDto));
        $this->assertSame($wrappingDto, $this->readService->get($id));
    }

    public function testCreate()
    {
        $id = 1;

        /** @var \Magento\GiftWrapping\Model\Wrapping|\PHPUnit_Framework_MockObject_MockObject $wrappingModel */
        $wrappingModel = $this->getMockBuilder('Magento\GiftWrapping\Model\Wrapping')->disableOriginalConstructor()
            ->setMethods([])->getMock();
        /** @var Data\Wrapping|\PHPUnit_Framework_MockObject_MockObject $wrappingDto */
        $wrappingDto = $this->getMockBuilder('Magento\GiftWrapping\Service\V1\Data\Wrapping')
            ->disableOriginalConstructor()->setMethods([])->getMock();

        $this->wrappingConverterMock->expects($this->once())->method('getModel')->with($wrappingDto)
            ->will($this->returnValue($wrappingModel));
        $wrappingModel->expects($this->at(0))->method('getId')->will($this->returnValue(null));
        $wrappingModel->expects($this->once())->method('save');
        $wrappingModel->expects($this->at(1))->method('getId')->will($this->returnValue($id));

        $this->assertEquals($id, $this->writeService->create($wrappingDto));
    }

    /**
     * @expectedException \Magento\Framework\Exception\InputException
     */
    public function testCreateException()
    {
        /** @var Data\Wrapping|\PHPUnit_Framework_MockObject_MockObject $wrappingDto */
        $wrappingDto = $this->getMockBuilder('Magento\GiftWrapping\Service\V1\Data\Wrapping')
            ->disableOriginalConstructor()->setMethods([])->getMock();

        $wrappingDto->expects($this->once())->method('getWrappingId')->will($this->returnValue(1));

        $this->writeService->create($wrappingDto);
    }

    public function testUpdate()
    {
        $id = 1;

        /** @var \Magento\GiftWrapping\Model\Wrapping|\PHPUnit_Framework_MockObject_MockObject $wrappingModel */
        $wrappingModel = $this->getMockBuilder('Magento\GiftWrapping\Model\Wrapping')->disableOriginalConstructor()
            ->setMethods([])->getMock();
        /** @var Data\Wrapping|\PHPUnit_Framework_MockObject_MockObject $wrappingDto */
        $wrappingDto = $this->getMockBuilder('Magento\GiftWrapping\Service\V1\Data\Wrapping')
            ->disableOriginalConstructor()->setMethods([])->getMock();

        $this->wrappingConverterMock->expects($this->once())->method('getModel')->with($wrappingDto, $id)
            ->will($this->returnValue($wrappingModel));
        $wrappingModel->expects($this->once())->method('save');
        $wrappingModel->expects($this->any())->method('getId')->will($this->returnValue($id));

        $this->assertEquals($id, $this->writeService->update($id, $wrappingDto));
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testUpdateException()
    {
        $id = 1;

        /** @var \Magento\GiftWrapping\Model\Wrapping|\PHPUnit_Framework_MockObject_MockObject $wrappingModel */
        $wrappingModel = $this->getMockBuilder('Magento\GiftWrapping\Model\Wrapping')->disableOriginalConstructor()
            ->setMethods([])->getMock();
        /** @var Data\Wrapping|\PHPUnit_Framework_MockObject_MockObject $wrappingDto */
        $wrappingDto = $this->getMockBuilder('Magento\GiftWrapping\Service\V1\Data\Wrapping')
            ->disableOriginalConstructor()->setMethods([])->getMock();

        $this->wrappingConverterMock->expects($this->once())->method('getModel')->with($wrappingDto, $id)
            ->will($this->returnValue($wrappingModel));
        $wrappingModel->expects($this->any())->method('getId')->will($this->returnValue(null));
        $this->writeService->update($id, $wrappingDto);
    }

    /**
     * @expectedException \Magento\Framework\Exception\InputException
     */
    public function testUpdateThrowsInputExceptionIfWrappingIdExists()
    {
        $id = 1;
        /** @var Data\Wrapping|\PHPUnit_Framework_MockObject_MockObject $wrappingDto */
        $wrappingDto = $this->getMock(
            'Magento\GiftWrapping\Service\V1\Data\Wrapping',
            ['getWrappingId'],
            [],
            '',
            false
        );
        $wrappingDto->expects($this->any())->method('getWrappingId')->will($this->returnValue(100));
        $this->writeService->update($id, $wrappingDto);
    }

    public function testSearch()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject $searchCriteria */
        $searchCriteria = $this->getMockBuilder('Magento\Framework\Api\SearchCriteria')
            ->disableOriginalConstructor()->setMethods([])->getMock();
        /** @var \PHPUnit_Framework_MockObject_MockObject $searchResults */
        $searchResults = $this->getMockBuilder('Magento\Framework\Api\SearchResults')
            ->disableOriginalConstructor()->setMethods([])->getMock();
        /** @var \Magento\GiftWrapping\Model\Wrapping|\PHPUnit_Framework_MockObject_MockObject $wrappingModel */
        $wrappingModel = $this->getMockBuilder('Magento\GiftWrapping\Model\Wrapping')->disableOriginalConstructor()
            ->setMethods([])->getMock();
        /** @var Data\Wrapping|\PHPUnit_Framework_MockObject_MockObject $wrappingDto */
        $wrappingDto = $this->getMockBuilder('Magento\GiftWrapping\Service\V1\Data\Wrapping')
            ->disableOriginalConstructor()->setMethods([])->getMock();

        $this->wrappingRepositoryMock->expects($this->once())->method('find')->with($searchCriteria)
            ->will($this->returnValue([$wrappingModel]));
        $this->wrappingMapperMock->expects($this->once())->method('extractDto')->with($wrappingModel)
            ->will($this->returnValue($wrappingDto));
        $this->searchResultsBuilderMock->expects($this->once())->method('setItems')->with([$wrappingDto])
            ->will($this->returnSelf());
        $this->searchResultsBuilderMock->expects($this->once())->method('setTotalCount')->with(1)
            ->will($this->returnSelf());
        $this->searchResultsBuilderMock->expects($this->once())->method('setSearchCriteria')->with($searchCriteria)
            ->will($this->returnSelf());
        $this->searchResultsBuilderMock->expects($this->once())->method('create')
            ->will($this->returnValue($searchResults));

        $this->assertSame($searchResults, $this->readService->search($searchCriteria));
    }

    public function testDelete()
    {
        $id = 1;
        /** @var \Magento\GiftWrapping\Model\Wrapping|\PHPUnit_Framework_MockObject_MockObject $wrappingModel */
        $wrappingModel = $this->getMockBuilder('Magento\GiftWrapping\Model\Wrapping')->disableOriginalConstructor()
            ->setMethods([])->getMock();

        $this->wrappingRepositoryMock->expects($this->once())->method('get')->with($id)
            ->will($this->returnValue($wrappingModel));
        $wrappingModel->expects($this->once())->method('delete');

        $this->assertTrue($this->writeService->delete(1));
    }
}
