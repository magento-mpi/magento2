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
     * @var \Magento\Framework\App\Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filesystem;

    /**
     * @var Minifier
     */
    protected $minifier;

    /**
     * @var string
     */
    protected $minifyDir = 'pub/cache/minify';

    /**
     * @var \Magento\Framework\Filesystem\Directory\Read|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rootDirectory;

    /**
     * @var \Magento\Framework\Filesystem\Directory\Read|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pubViewCacheDir;

    /**
     * Creat test mocks
     */
    protected function setUp()
    {
        $this->strategy = $this->getMockForAbstractClass('Magento\Code\Minifier\StrategyInterface');
        $this->filesystem = $this->getMock(
            'Magento\Framework\App\Filesystem',
            array('getDirectoryRead', '__wakeup'),
            array(),
            '',
            false
        );
        $this->rootDirectory = $this->getMock(
            'Magento\Framework\Filesystem\Directory\Read',
            array('getRelativePath', 'isExist', 'getAbsolutePath'),
            array(),
            '',
            false
        );
        $this->pubViewCacheDir = $this->getMock(
            'Magento\Framework\Filesystem\Directory\Read',
            array('getAbsolutePath', 'getRelativePath'),
            array(),
            '',
            false
        );
        $this->filesystem->expects(
            $this->at(0)
        )->method(
            'getDirectoryRead'
        )->with(
            \Magento\Framework\App\Filesystem::ROOT_DIR
        )->will(
            $this->returnValue($this->rootDirectory)
        );
        $this->filesystem->expects(
            $this->at(1)
        )->method(
            'getDirectoryRead'
        )->with(
            \Magento\Framework\App\Filesystem::PUB_VIEW_CACHE_DIR
        )->will(
            $this->returnValue($this->pubViewCacheDir)
        );
        $this->minifier = new Minifier($this->strategy, $this->filesystem, $this->minifyDir);
    }

    /**
     * Test for getMinifiedFile
     */
    public function testGetMinifiedFile()
    {
        $originalFile = 'basedir/pub/lib/original/some.js';
        $originalFileRelative = 'pub/lib/original/some.js';
        $originalMinifiedFileRelative = 'pub/lib/original/some.min.js';
        $minifiedFileGeneratedPattern = $this->minifyDir . '%ssome.min.js';

        $this->rootDirectory->expects(
            $this->at(0)
        )->method(
            'getRelativePath'
        )->with(
            $originalFile
        )->will(
            $this->returnValue($originalFileRelative)
        );

        $this->rootDirectory->expects(
            $this->at(1)
        )->method(
            'isExist'
        )->with(
            $originalMinifiedFileRelative
        )->will(
            $this->returnValue(false)
        );

        $this->strategy->expects(
            $this->once()
        )->method(
            'minifyFile'
        )->with(
            $originalFileRelative,
            $this->matches($minifiedFileGeneratedPattern)
        );
        $minifiedFile = $this->minifier->getMinifiedFile($originalFile);
        $this->assertStringMatchesFormat($this->minifyDir . '%ssome.min.js', $minifiedFile);
    }

    /**
     * Test for getMinifiedFile (in case when minified file is passed)
     */
    public function testGetMinifiedFileOriginalMinified()
    {
        $originalFile = 'file.min.js';
        $this->strategy->expects($this->never())->method('minifyFile');
        $minifiedFile = $this->minifier->getMinifiedFile($originalFile);
        $this->assertSame($originalFile, $minifiedFile);
    }

    /**
     * Test for getMinifiedFile (in case when minified file exists)
     */
    public function testGetMinifiedFileExistsMinified()
    {
        $originalAbsolutePath = 'basedir/pub/lib/original/some.js';
        $originalRelativePath = 'pub/lib/original/some.js';
        $originalMinifiedRelativePath = 'pub/lib/original/some.min.js';
        $originalMinifiedAbsolutePath = 'basedir/pub/lib/original/some.min.js';

        $this->rootDirectory->expects(
            $this->at(0)
        )->method(
            'getRelativePath'
        )->with(
            $originalAbsolutePath
        )->will(
            $this->returnValue($originalRelativePath)
        );

        $this->rootDirectory->expects(
            $this->at(1)
        )->method(
            'isExist'
        )->with(
            $originalMinifiedRelativePath
        )->will(
            $this->returnValue(true)
        );

        $this->rootDirectory->expects(
            $this->at(2)
        )->method(
            'getAbsolutePath'
        )->with(
            $originalMinifiedRelativePath
        )->will(
            $this->returnValue($originalMinifiedAbsolutePath)
        );

        $this->assertEquals($originalMinifiedAbsolutePath, $this->minifier->getMinifiedFile($originalAbsolutePath));
    }
}
