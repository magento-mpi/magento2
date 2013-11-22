<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Code\Minifier\Strategy;

use Magento\Filesystem,
    Magento\Filesystem\DirectoryList,
    Magento\Filesystem\Directory\Write;

class LiteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Filesystem | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $filesystem;

    /**
     * @var Write | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $directory;

    /**
     * @var \Magento\Code\Minifier\AdapterInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $adapter;

    /**
     * Set up before each test
     */
    public function setUp()
    {
        $this->directory = $this->getMock(
            'Magento\Filesystem\Directory\Write',
            array('stat', 'isExist', 'readFile', 'writeFile', 'touch'), array(), '', false
        );
        $this->filesystem = $this->getMock('Magento\Filesystem', array('getDirectoryWrite', '__wakeup'), array(), '', false);
        $this->filesystem->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::PUB_VIEW_CACHE)
            ->will($this->returnValue($this->directory));
        $this->adapter = $this->getMockForAbstractClass('Magento\Code\Minifier\AdapterInterface', array(), '', false);
    }

    /**
     * Test for minifyFile if case update is needed
     */
    public function testGetMinifiedFile()
    {
        $originalFile = __DIR__ . '/original/some.js';
        $minifiedFile = __DIR__ . '/minified/some.min.js';
        $content = 'content';
        $minifiedContent = 'minified content';

        $this->directory->expects($this->once())
            ->method('readFile')
            ->with($originalFile)
            ->will($this->returnValue($content));
        $this->directory->expects($this->once())
            ->method('writeFile')
            ->with($minifiedFile, $minifiedContent);

        $this->adapter->expects($this->once())
            ->method('minify')
            ->with($content)
            ->will($this->returnValue($minifiedContent));

        $strategy = new Lite($this->adapter, $this->filesystem);
        $strategy->minifyFile($originalFile, $minifiedFile);
    }

    /**
     * Test for minifyFile if case update is NOT needed
     */
    public function testGetMinifiedFileNoUpdateNeeded()
    {
        $originalFile = __DIR__ . '/original/some.js';
        $minifiedFile = __DIR__ . '/some.min.js';

        $this->directory->expects($this->once())
            ->method('isExist')
            ->with($minifiedFile)
            ->will($this->returnValue(true));

        $this->directory->expects($this->never())
            ->method('readFile');
        $this->directory->expects($this->never())
            ->method('writeFile');

        $this->adapter->expects($this->never())->method('minify');

        $strategy = new Lite($this->adapter, $this->filesystem);
        $strategy->minifyFile($originalFile, $minifiedFile);
    }
}
