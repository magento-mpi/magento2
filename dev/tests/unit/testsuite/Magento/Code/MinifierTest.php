<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Code;

class MinifierTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Code\Minifier\StrategyInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $strategy;

    /**
     * @var \Magento\Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filesystem;

    /**
     * @var \Magento\Code\Minifier
     */
    protected $minifier;

    /**
     * @var string
     */
    protected $minifyDir = 'minify';

    /**
     * @var \Magento\Filesystem\Directory\Write|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $directoryRead;

    /**
     * Creat test mocks
     */
    protected function setUp()
    {
        $this->strategy = $this->getMockForAbstractClass('Magento\Code\Minifier\StrategyInterface');
        $this->filesystem = $this->getMock('Magento\Filesystem', array('getDirectoryRead'), array(), '', false);
        $this->directoryRead = $this->getMock(
            'Magento\Filesystem\Directory\Write',
            array('getRelativePath', 'getAbsolutePath', 'isExist'), array(), '', false
        );
        $this->filesystem->expects($this->once())
            ->method('getDirectoryRead')
            ->with(\Magento\Filesystem\DirectoryList::PUB_VIEW_CACHE)
            ->will($this->returnValue($this->directoryRead));
        $this->minifier = new \Magento\Code\Minifier($this->strategy, $this->filesystem, $this->minifyDir);
    }

    /**
     * Test for getMinifiedFile
     */
    public function testGetMinifiedFile()
    {
        $originalFile = '/pub/cache/' . $this->minifyDir . '/original/some.js';

        $this->directoryRead->expects($this->at(0))
            ->method('getRelativePath')
            ->with('/pub/cache/' . $this->minifyDir . '/original/some.min.js')
            ->will($this->returnValue($this->minifyDir . '/original/some.min.js'));

        $this->directoryRead->expects($this->at(1))
            ->method('isExist')
            ->with($this->matches($this->minifyDir . '/original/some.min.js'))
            ->will($this->returnValue(false));

        $this->directoryRead->expects($this->at(2))
            ->method('getAbsolutePath')
            ->with($this->matches($this->minifyDir . '%ssome.min.js'))
            ->will($this->returnValue('/pub/cache/' . $this->minifyDir . '/original/some.min.js'));

        $this->strategy->expects($this->once())
            ->method('minifyFile')
            ->with($originalFile, $this->matches('/pub/cache/' . $this->minifyDir . '%ssome.min.js'));
        $minifiedFile = $this->minifier->getMinifiedFile($originalFile);
        $this->assertStringMatchesFormat('/pub/cache/' . $this->minifyDir . '%ssome.min.js', $minifiedFile);
    }

    /**
     * Test for getMinifiedFile (in case when minified file is passed)
     */
    public function testGetMinifiedFileOriginalMinified()
    {
        $originalFile = 'file.min.js';
        $this->strategy->expects($this->never())
            ->method('minifyFile');
        $minifiedFile = $this->minifier->getMinifiedFile($originalFile);
        $this->assertSame($originalFile, $minifiedFile);
    }

    /**
     * Test for getMinifiedFile (in case when minified file exists)
     */
    public function testGetMinifiedFileExistsMinified()
    {
        $originalFile = '/pub/cache/' . $this->minifyDir . '/original/some.js';
        $expectedMinifiedFile = '/pub/cache/' . $this->minifyDir . '/original/some.min.js';

        $this->directoryRead->expects($this->at(0))
            ->method('getRelativePath')
            ->with($expectedMinifiedFile)
            ->will($this->returnValue($this->minifyDir . '/original/some.min.js'));

        $this->directoryRead->expects($this->at(1))
            ->method('isExist')
            ->with($this->matches($this->minifyDir . '/original/some.min.js'))
            ->will($this->returnValue(true));

        $minifiedFile = $this->minifier->getMinifiedFile($originalFile);
        $this->assertStringEndsWith($expectedMinifiedFile, $minifiedFile);
    }
}
