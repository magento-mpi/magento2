<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Code\Minifier\Strategy;

use Magento\Framework\App\Filesystem;
use Magento\Framework\Filesystem\Directory\Read;
use Magento\Framework\Filesystem\Directory\Write;

class LiteTest extends \PHPUnit_Framework_TestCase
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
     * @var \Magento\Framework\Code\Minifier\AdapterInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $adapter;

    /**
     * Set up before each test
     */
    public function setUp()
    {
        $this->rootDirectory = $this->getMock(
            'Magento\Framework\Filesystem\Directory\Read',
            array(),
            array(),
            '',
            false
        );
        $this->pubViewCacheDir = $this->getMock(
            'Magento\Framework\Filesystem\Directory\Write',
            array(),
            array(),
            '',
            false
        );
        $this->filesystem = $this->getMock(
            'Magento\Framework\App\Filesystem',
            array('getDirectoryWrite', 'getDirectoryRead', '__wakeup'),
            array(),
            '',
            false
        );
        $this->filesystem->expects(
            $this->once()
        )->method(
            'getDirectoryRead'
        )->with(
            \Magento\Framework\App\Filesystem::ROOT_DIR
        )->will(
            $this->returnValue($this->rootDirectory)
        );
        $this->filesystem->expects(
            $this->once()
        )->method(
            'getDirectoryWrite'
        )->with(
            \Magento\Framework\App\Filesystem::PUB_VIEW_CACHE_DIR
        )->will(
            $this->returnValue($this->pubViewCacheDir)
        );
        $this->adapter = $this->getMockForAbstractClass(
            'Magento\Framework\Code\Minifier\AdapterInterface',
            array(),
            '',
            false
        );
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

        $this->rootDirectory->expects(
            $this->once()
        )->method(
            'readFile'
        )->with(
            $originalFile
        )->will(
            $this->returnValue($content)
        );
        $this->pubViewCacheDir->expects($this->once())->method('writeFile')->with($minifiedFile, $minifiedContent);

        $this->adapter->expects(
            $this->once()
        )->method(
            'minify'
        )->with(
            $content
        )->will(
            $this->returnValue($minifiedContent)
        );

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

        $this->pubViewCacheDir->expects(
            $this->once()
        )->method(
            'isExist'
        )->with(
            $minifiedFile
        )->will(
            $this->returnValue(true)
        );

        $this->rootDirectory->expects($this->never())->method('readFile');
        $this->pubViewCacheDir->expects($this->never())->method('writeFile');

        $this->adapter->expects($this->never())->method('minify');

        $strategy = new Lite($this->adapter, $this->filesystem);
        $strategy->minifyFile($originalFile, $minifiedFile);
    }
}
