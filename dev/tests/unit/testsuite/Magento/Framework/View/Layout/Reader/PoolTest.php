<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\View\Layout\Reader;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class PoolTest extends \PHPUnit_Framework_TestCase
{
    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Framework\View\Layout\Reader\Pool */
    protected $pool;

    /** @var \Magento\Framework\View\Layout\ReaderFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $readerFactoryMock;

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->readerFactoryMock = $this->getMockBuilder('Magento\Framework\View\Layout\ReaderFactory')
            ->disableOriginalConstructor()->getMock();

        $this->pool = $this->objectManagerHelper->getObject(
            'Magento\Framework\View\Layout\Reader\Pool',
            [
                'readerFactory' => $this->readerFactoryMock,
                'readers' => [
                    'move' => 'Magento\Framework\View\Layout\Reader\Move',
                    'remove' => 'Magento\Framework\View\Layout\Reader\Remove',
                ]
            ]
        );
    }

    public function testReadStructure()
    {
        /** @var Context $contextMock */
        $contextMock = $this->getMockBuilder('Magento\Framework\View\Layout\Reader\Context')
            ->disableOriginalConstructor()->getMock();

        $currentElement = new \Magento\Framework\View\Layout\Element(
            '<element><move name="block"/><remove name="container"/><ignored name="user"/></element>'
        );

        /**
         * @var \Magento\Framework\View\Layout\Reader\Move|\PHPUnit_Framework_MockObject_MockObject $moveReaderMock
         */
        $moveReaderMock = $this->getMockBuilder('Magento\Framework\View\Layout\Reader\Move')
            ->disableOriginalConstructor()->getMock();
        $moveReaderMock->expects($this->once())->method('process')
            ->willReturn($this->returnSelf());
        $moveReaderMock->method('getSupportedNodes')
            ->willReturn(['move']);

        /**
         * @var \Magento\Framework\View\Layout\Reader\Remove|\PHPUnit_Framework_MockObject_MockObject $removeReaderMock
         */
        $removeReaderMock = $this->getMockBuilder('Magento\Framework\View\Layout\Reader\Remove')
            ->disableOriginalConstructor()->getMock();
        $removeReaderMock->expects($this->once())->method('process')
            ->with()
            ->willReturn($this->returnSelf());
        $removeReaderMock->method('getSupportedNodes')
            ->willReturn(['remove']);

        $this->readerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValueMap(
                [
                    ['Magento\Framework\View\Layout\Reader\Move', [], $moveReaderMock],
                    ['Magento\Framework\View\Layout\Reader\Remove', [], $removeReaderMock]
                ]
            ));

        $this->pool->readStructure($contextMock, $currentElement);
    }
}
