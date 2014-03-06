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
    const MINIFY_ABS_DIR = '/absolute/path/_cache/minified';
    const MINIFY_REL_DIR = '_cache/minified';

    /**
     * @var \Magento\Code\Minifier\StrategyInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $strategy;

    /**
     * @var \Magento\App\Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filesystem;

    /**
     * @var Minifier
     */
    protected $minifier;

    /**
     * @var \Magento\Filesystem\Directory\Read|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rootDirectory;

    /**
     * @var \Magento\Filesystem\Directory\Read|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pubStaticViewDir;

    /**
     * Creat test mocks
     */
    protected function setUp()
    {
        $this->strategy = $this->getMockForAbstractClass('Magento\Code\Minifier\StrategyInterface');
        $this->filesystem = $this->getMock(
            'Magento\App\Filesystem',
            array('getDirectoryRead', '__wakeup'),
            array(),
            '',
            false
        );
        $this->rootDirectory = $this->getMock(
            'Magento\Filesystem\Directory\Read',
            array('getRelativePath', 'isExist'), array(), '', false
        );
        $this->pubStaticViewDir = $this->getMock(
            'Magento\Filesystem\Directory\Read',
            array('getAbsolutePath', 'getRelativePath'),
            array(),
            '',
            false
        );
        $this->filesystem->expects($this->at(0))
            ->method('getDirectoryRead')
            ->with(\Magento\App\Filesystem::ROOT_DIR)
            ->will($this->returnValue($this->rootDirectory));
        $this->filesystem->expects($this->at(1))
            ->method('getDirectoryRead')
            ->with(\Magento\App\Filesystem::STATIC_VIEW_DIR)
            ->will($this->returnValue($this->pubStaticViewDir));
        $this->minifier = new Minifier($this->strategy, $this->filesystem, self::MINIFY_ABS_DIR);
    }

    /**
     * Test for getMinifiedFile
     */
    public function testGetMinifiedFile()
    {
        $originalFile = '/pub/static/' . self::MINIFY_REL_DIR . '/original/some.js';
        $originalFileRelative = self::MINIFY_REL_DIR . '/original/some.js';
        $minifiedFileRelative = self::MINIFY_REL_DIR . '/original/some.min.js';

        $this->rootDirectory->expects($this->at(0))
            ->method('getRelativePath')
            ->with($originalFile)
            ->will($this->returnValue($originalFileRelative));

        $this->rootDirectory->expects($this->at(1))
            ->method('isExist')
            ->with($minifiedFileRelative)
            ->will($this->returnValue(false));

        $this->pubStaticViewDir->expects($this->once())
            ->method('getAbsolutePath')
            ->with($this->matches(self::MINIFY_REL_DIR . '%ssome.min.js'))
            ->will($this->returnValue('/pub/static/' . self::MINIFY_REL_DIR . '/original/some.min.js'));

        $this->pubStaticViewDir->expects($this->any())
            ->method('getRelativePath')
            ->with($this->matches(self::MINIFY_ABS_DIR . '%ssome.min.js'))
            ->will($this->returnValue(self::MINIFY_REL_DIR . '/original/some.min.js'));

        $this->strategy->expects($this->once())
            ->method('minifyFile')
            ->with(self::MINIFY_REL_DIR . '/original/some.js', $this->matches(self::MINIFY_REL_DIR . '%ssome.min.js'));
        $minifiedFile = $this->minifier->getMinifiedFile($originalFile);
        $this->assertStringMatchesFormat('/pub/static/' . self::MINIFY_REL_DIR . '%ssome.min.js', $minifiedFile);
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
        $originalFile = '/pub/static/' . self::MINIFY_REL_DIR . '/original/some.js';
        $expectedMinifiedFile = '/pub/static/' . self::MINIFY_REL_DIR . '/original/some.min.js';
        $expectedMinifiedFileRelative = self::MINIFY_REL_DIR . '/original/some.min.js';

        $this->rootDirectory->expects($this->at(0))
            ->method('getRelativePath')
            ->with($originalFile)
            ->will($this->returnValue(self::MINIFY_REL_DIR . '/original/some.js'));

        $this->rootDirectory->expects($this->at(1))
            ->method('isExist')
            ->with(self::MINIFY_REL_DIR . '/original/some.min.js')
            ->will($this->returnValue(true));

        $this->pubStaticViewDir->expects($this->once())
            ->method('getAbsolutePath')
            ->with(self::MINIFY_REL_DIR . '/original/some.min.js')
            ->will($this->returnValue($expectedMinifiedFile));

        $this->pubStaticViewDir->expects($this->once())
            ->method('getRelativePath')
            ->with(self::MINIFY_REL_DIR . '/original/some.min.js')
            ->will($this->returnValue($expectedMinifiedFileRelative));

        $minifiedFile = $this->minifier->getMinifiedFile($originalFile);
        $this->assertStringEndsWith($minifiedFile, $expectedMinifiedFile);
    }
}
