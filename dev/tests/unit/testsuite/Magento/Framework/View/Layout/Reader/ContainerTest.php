<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\View\Layout\Reader;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Framework\View\Layout\ScheduledStructure as ScheduledStructure;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var Container|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $container;

    /**
     * @var \Magento\Framework\View\Layout\ScheduledStructure\Helper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helperMock;

    /**
     * @var \Magento\Framework\View\Layout\Reader\Pool|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $readerPoolMock;

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->helperMock = $this->getMockBuilder('Magento\Framework\View\Layout\ScheduledStructure\Helper')
            ->disableOriginalConstructor()->getMock();
        $this->readerPoolMock = $this->getMockBuilder('Magento\Framework\View\Layout\Reader\Pool')
            ->disableOriginalConstructor()->getMock();

        $this->container = $this->objectManagerHelper->getObject(
            'Magento\Framework\View\Layout\Reader\Container',
            [
                'helper' => $this->helperMock,
                'readerPool' => $this->readerPoolMock
            ]
        );
    }

    /**
     * @param \Magento\Framework\View\Layout\Element $elementCurrent
     * @param string $containerName
     * @param \Magento\Framework\View\Layout\Element $elementParent
     * @param array $structureElement
     * @param int $helperCalls
     * @param array $expectedData
     *
     * @dataProvider processDataProvider
     */
    public function testProcess(
        $elementCurrent,
        $containerName,
        $elementParent,
        $structureElement,
        $helperCalls,
        $expectedData
    ) {

        /** @var ScheduledStructure|\PHPUnit_Framework_MockObject_MockObject $scheduledStructureMock */
        $scheduledStructureMock = $this->getMockBuilder('Magento\Framework\View\Layout\ScheduledStructure')
            ->disableOriginalConstructor()->getMock();
        $scheduledStructureMock->expects($this->once())
            ->method('getStructureElement')
            ->with($containerName, [])
            ->willReturn($structureElement);
        $scheduledStructureMock->expects($this->once())
            ->method('setStructureElement')
            ->with($containerName, $expectedData)
            ->willReturnSelf();

        /** @var Context|\PHPUnit_Framework_MockObject_MockObject $contextMock */
        $contextMock = $this->getMockBuilder('Magento\Framework\View\Layout\Reader\Context')
            ->disableOriginalConstructor()->getMock();
        $contextMock->expects($this->any())
            ->method('getScheduledStructure')
            ->willReturn($scheduledStructureMock);

        $this->helperMock->expects($this->exactly($helperCalls))
            ->method('scheduleStructure')
            ->with($scheduledStructureMock, $elementCurrent, $elementParent);

        $this->readerPoolMock->expects($this->once())
            ->method('readStructure')
            ->with($contextMock, $elementCurrent)
            ->willReturnSelf();

        $this->container->process($contextMock, $elementCurrent, $elementParent);
    }

    /**
     * @return array
     */
    public function processDataProvider()
    {
        return [
            'container' => [
                'elementCurrent' => new \Magento\Framework\View\Layout\Element(
                    '<container name="container" id="id_add" tag="body"/>'
                ),
                'containerName' => 'container',
                'elementParent' => new \Magento\Framework\View\Layout\Element('<parent_element/>'),
                'structureElement' => [
                    Container::STRUCTURE_INDEX_DATA => [
                        'attributes' => [
                            'id' => 'id_value',
                            'tag' => 'tag_value',
                            'unchanged' => 'unchanged_value',
                        ]
                    ]
                ],
                'helperCalls' => 1,
                'expectedData' => [
                    Container::STRUCTURE_INDEX_DATA => [
                        'attributes' => [
                            'id' => 'id_add',
                            'tag' => 'body',
                            'unchanged' => 'unchanged_value',
                        ]
                    ]
                ]
            ],
            'referenceContainer' => [
                'elementCurrent' => new \Magento\Framework\View\Layout\Element(
                    '<referenceContainer name="reference" htmlTag="span" htmlId="id_add" htmlClass="new" label="Add"/>'
                ),
                'containerName' => 'reference',
                'elementParent' => new \Magento\Framework\View\Layout\Element('<parent_element/>'),
                'structureElement' => [],
                'helperCalls' => 0,
                'expectedData' => [
                    Container::STRUCTURE_INDEX_DATA => [
                        'attributes' => [
                            Container::CONTAINER_OPT_HTML_TAG   => 'span',
                            Container::CONTAINER_OPT_HTML_ID    => 'id_add',
                            Container::CONTAINER_OPT_HTML_CLASS => 'new',
                            Container::CONTAINER_OPT_LABEL      => 'Add',
                        ]
                    ]
                ]
            ]
        ];
    }
}
