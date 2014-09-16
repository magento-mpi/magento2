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
    protected $processor;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Filesystem
     */
    protected $filesystem;

    protected function setUp()
    {
        $this->filesystem = $this->getMock('Magento\Framework\Filesystem', ['getDirectoryWrite'], [], '', false);
        $this->processor = $this->getMock('Magento\Indexer\Model\Processor', [], [], '', false);
        $this->entryPoint = new Indexer('reportDir', $this->filesystem, $this->processor);
    }

    public function testExecute()
    {
        $dir = $this->getMock('Magento\Framework\Filesystem\Directory\Write', [], [], '', false);
        $dir->expects($this->any())->method('getRelativePath')->will($this->returnArgument(0));
        $this->filesystem->expects($this->once())->method('getDirectoryWrite')->will($this->returnValue($dir));
        $this->processor->expects($this->once())->method('reindexAll');
        $this->assertEquals('0', $this->entryPoint->launch());
    }

    public function testCatchException()
    {
        $bootstrap = $this->getMock('Magento\Framework\App\Bootstrap', [], [], '', false);
        $this->assertFalse($this->entryPoint->catchException($bootstrap, new \Exception));
    }
}
