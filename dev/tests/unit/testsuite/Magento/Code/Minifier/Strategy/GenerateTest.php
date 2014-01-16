<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Code\Minifier\Strategy;

use Magento\Filesystem,
    Magento\Filesystem\Directory\Write,
    Magento\Filesystem\Directory\Read;

class GenerateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Filesystem | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $filesystem;

    /**
     * @var Read | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rootDirectory;

    /**
     * @var Write | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $pubViewCacheDir;

    /**
     * @var \Magento\Code\Minifier\AdapterInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $adapter;

    /**
     * Set up before each test
     */
    public function setUp()
    {
        $this->rootDirectory = $this->getMock(
            'Magento\Filesystem\Directory\Read',
            array(), array(), '', false
        );
        $this->pubViewCacheDir = $this->getMock(
            'Magento\Filesystem\Directory\Write',
            array(), array(), '', false
        );
        $this->filesystem = $this->getMock(
            'Magento\Filesystem',
            array('getDirectoryWrite', 'getDirectoryRead', '__wakeup'),
            array(), '', false
        );
        $this->filesystem->expects($this->once())
            ->method('getDirectoryRead')
            ->with(\Magento\Filesystem::ROOT)
            ->will($this->returnValue($this->rootDirectory));
        $this->filesystem->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(\Magento\Filesystem::PUB_VIEW_CACHE)
            ->will($this->returnValue($this->pubViewCacheDir));
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

        $this->rootDirectory->expects($this->once())
            ->method('readFile')
            ->with($originalFile)
            ->will($this->returnValue($content));
        $this->pubViewCacheDir->expects($this->once())
            ->method('getRelativePath')
            ->will($this->returnArgument(0));
        $this->pubViewCacheDir->expects($this->once())
            ->method('writeFile')
            ->with($minifiedFile, $minifiedContent);

        $this->adapter->expects($this->once())
            ->method('minify')
            ->with($content)
            ->will($this->returnValue($minifiedContent));

        $strategy = new Generate($this->adapter, $this->filesystem);
        $strategy->minifyFile($originalFile, $minifiedFile);
    }

    /**
     * Test for minifyFile if case update is NOT needed
     */
    public function testGetMinifiedFileNoUpdateNeeded()
    {
        $originalFile = __DIR__ . '/original/some.js';
        $minifiedFile = __DIR__ . '/some.min.js';

        $mTimeMap = array(
            array($originalFile, null, array('mtime' => 1)),
            array($minifiedFile, null, array('mtime' => 1)),
        );

        $this->pubViewCacheDir->expects($this->once())
            ->method('isExist')
            ->with($minifiedFile)
            ->will($this->returnValue(true));
        $this->rootDirectory->expects($this->once())
            ->method('stat')
            ->will($this->returnValueMap($mTimeMap));
        $this->pubViewCacheDir->expects($this->once())
            ->method('stat')
            ->will($this->returnValueMap($mTimeMap));

        $this->rootDirectory->expects($this->never())
            ->method('readFile');
        $this->pubViewCacheDir->expects($this->never())
            ->method('writeFile');

        $this->adapter->expects($this->never())->method('minify');

        $strategy = new Generate($this->adapter, $this->filesystem);
        $strategy->minifyFile($originalFile, $minifiedFile);
    }
}
