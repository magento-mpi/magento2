<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Indexer\App;

class IndexerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Indexer\App\Indexer
     */
    protected $entryPoint;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Indexer\Model\Processor
     */
    protected $processorMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Filesystem
     */
    protected $filesystemMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\App\Console\Response
     */
    protected $responseMock;

    protected function setUp()
    {
        $this->responseMock = $this->getMock('Magento\App\Console\Response', array('setCode'), array(), '', false);
        $this->filesystemMock = $this->getMock('Magento\Filesystem', array('getDirectoryWrite'), array(), '', false);
        $directoryMock = $this->getMock('Magento\Filesystem\Directory\Write', array(), array(), '', false);
        $directoryMock->expects($this->any())
            ->method('getRelativePath')
            ->will($this->returnArgument(0));
        $this->filesystemMock->expects($this->once())
            ->method('getDirectoryWrite')
            ->will($this->returnValue($directoryMock));
        $this->processorMock = $this->getMock('Magento\Indexer\Model\Processor', array(), array(), '', false);
        $this->entryPoint = new \Magento\Indexer\App\Indexer(
            'reportDir',
            $this->filesystemMock,
            $this->processorMock,
            $this->responseMock
        );
    }

    public function testLaunch()
    {
        $this->responseMock->expects($this->once())
            ->method('setCode')
            ->with(0);
        $this->processorMock->expects($this->once())->method('reindexAll');
        $this->assertSame($this->responseMock, $this->entryPoint->launch());
    }
}
