<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Index\App;

class IndexerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Index\App\Indexer
     */
    protected $_entryPoint;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Index\Model\IndexerFactory
     */
    protected $_indexFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystem;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_response;

    protected function setUp()
    {
        $this->_filesystem = $this->getMock('Magento\Framework\App\Filesystem', ['getDirectoryWrite'], [], '', false);
        $this->_indexFactory = $this->getMock('Magento\Index\Model\IndexerFactory', ['create'], [], '', false);
        $this->_response = $this->getMock('Magento\Framework\App\Console\Response', [], [], '', false);
        $this->_entryPoint = new Indexer('reportDir', $this->_filesystem, $this->_indexFactory, $this->_response);
    }

    /**
     * @param bool $value
     * @dataProvider executeDataProvider
     */
    public function testLaunch($value)
    {
        $dir = $this->getMock('Magento\Framework\Filesystem\Directory\Write', [], [], '', false);
        $dir->expects($this->any())->method('getRelativePath')->will($this->returnArgument(0));
        $this->_filesystem->expects($this->once())->method('getDirectoryWrite')->will($this->returnValue($dir));
        $process = $this->getMock(
            'Magento\Index\Model\Process',
            ['getIndexer', 'reindexEverything', '__wakeup'],
            [],
            '',
            false
        );
        $indexer = $this->getMock('Magento\Index\Model\Indexer', array('getProcessesCollection'), [], '', false);
        $indexerInterface = $this->getMock('Magento\Index\Model\IndexerInterface');
        $this->_indexFactory->expects($this->once())->method('create')->will($this->returnValue($indexer));
        $indexer->expects($this->once())->method('getProcessesCollection')->will($this->returnValue(array($process)));
        $process->expects($this->any())->method('getIndexer')->will($this->returnValue($indexerInterface));

        if ($value) {
            $indexerInterface->expects($this->once())->method('isVisible')->will($this->returnValue(true));
            $process->expects($this->once())->method('reindexEverything');
        } else {
            $indexerInterface->expects($this->once())->method('isVisible')->will($this->returnValue(false));
            $process->expects($this->never())->method('reindexEverything');
        }
        $this->assertEquals($this->_response, $this->_entryPoint->launch());
    }

    /**
     * @return array
     */
    public function executeDataProvider()
    {
        return [[true], [false]];
    }

    public function testCatchException()
    {
        $bootstrap = $this->getMock('Magento\Framework\App\Bootstrap', [], [], '', false);
        $this->assertFalse($this->_entryPoint->catchException($bootstrap, new \Exception));
    }
}
