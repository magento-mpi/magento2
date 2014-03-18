<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\RequireJs;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\RequireJs\Config\File\Collector\Aggregated|\PHPUnit_Framework_MockObject_MockObject
     */
    private $fileSource;

    /**
     * @var \Magento\View\DesignInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $design;

    /**
     * @var \Magento\Filesystem\Directory\ReadInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $baseDir;

    /**
     * @var \Magento\View\Asset\PathGenerator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $path;

    /**
     * @var \Magento\UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $baseUrl;

    /**
     * @var Config
     */
    private $object;

    protected function setUp()
    {
        $this->fileSource = $this->getMock(
            '\Magento\RequireJs\Config\File\Collector\Aggregated', array(), array(), '', false
        );
        $this->design = $this->getMockForAbstractClass('\Magento\View\DesignInterface');
        $this->path = $this->getMock('\Magento\View\Asset\PathGenerator', array(), array(), '', false);
        $this->baseUrl = $this->getMockForAbstractClass('\Magento\UrlInterface');

        $this->baseDir = $this->getMockForAbstractClass('\Magento\Filesystem\Directory\ReadInterface');
        $filesystem = $this->getMock('\Magento\App\Filesystem', array(), array(), '', false);
        $filesystem->expects($this->once())
            ->method('getDirectoryRead')
            ->with(\Magento\App\Filesystem::ROOT_DIR)
            ->will($this->returnValue($this->baseDir));

        $this->object = new \Magento\RequireJs\Config(
            $this->fileSource, $this->design, $filesystem, $this->path, $this->baseUrl
        );
    }

    public function testGetConfig()
    {
        $this->baseDir->expects($this->any())
            ->method('getRelativePath')
            ->will($this->returnCallback(function ($path) {
                return 'relative/' . $path;
            }));
        $this->baseDir->expects($this->any())
            ->method('readFile')
            ->will($this->returnCallback(function ($file) {
                return $file . ' content';
            }));
        $fileOne = $this->getMock('\Magento\View\File', array(), array(), '', false);
        $fileOne->expects($this->once())
            ->method('getFilename')
            ->will($this->returnValue('file_one.js'));
        $fileOne->expects($this->once())
            ->method('getModule')
            ->will($this->returnValue('Module_One'));
        $fileTwo = $this->getMock('\Magento\View\File', array(), array(), '', false);
        $fileTwo->expects($this->once())
            ->method('getFilename')
            ->will($this->returnValue('file_two.js'));
        $theme = $this->getMockForAbstractClass('\Magento\View\Design\ThemeInterface');
        $this->design->expects($this->once())
            ->method('getDesignTheme')
            ->will($this->returnValue($theme));
        $this->fileSource->expects($this->once())
            ->method('getFiles')
            ->with($theme, Config::CONFIG_FILE_NAME)
            ->will($this->returnValue(array($fileOne, $fileTwo)));

        $expected = <<<expected
(function(require){
relative/%s/paths-updater.js content

(function() {
relative/file_one.js content
require.config(mageUpdateConfigPaths(config, 'Module_One'))
})();
(function() {
relative/file_two.js content
require.config(mageUpdateConfigPaths(config, ''))
})();

})(require);
expected;

        $actual = $this->object->getConfig();
        $this->assertStringMatchesFormat($expected, $actual);
    }

    public function testGetConfigFileRelativePath()
    {
        $this->mockContextPath();
        $actual = $this->object->getConfigFileRelativePath();
        $this->assertSame('_requirejs/area/theme/locale/requirejs-config.js', $actual);
    }

    public function testGetBaseConfig()
    {
        $this->mockContextPath();
        $this->baseUrl->expects($this->once())
            ->method('getBaseUrl')
            ->with(array('_type' => \Magento\UrlInterface::URL_TYPE_STATIC))
            ->will($this->returnValue('http://base.url/'));
        $expected = <<<expected
require.config({
    "baseUrl": "http://base.url/area/theme/locale",
    "paths": {
        "magento": "mage/requirejs/plugin/id-normalizer"
    },
    "waitSeconds": 0
});

expected;
        $actual = $this->object->getBaseConfig();
        $this->assertSame($expected, $actual);
    }

    public function testGetConfigUrl()
    {
        $this->mockContextPath();
        $this->baseUrl->expects($this->once())
            ->method('getBaseUrl')
            ->with(array('_type' => \Magento\UrlInterface::URL_TYPE_STATIC))
            ->will($this->returnValue('http://base.url/'));
        $expected = 'http://base.url/_requirejs/area/theme/locale/requirejs-config.js';
        $actual = $this->object->getConfigUrl();
        $this->assertSame($expected, $actual);
    }

    public function testGetBaseUrl()
    {
        $expected = 'http://base.url/';
        $this->baseUrl->expects($this->once())
            ->method('getBaseUrl')
            ->with(array('_type' => \Magento\UrlInterface::URL_TYPE_STATIC))
            ->will($this->returnValue($expected));
        $actual = $this->object->getBaseUrl();
        $this->assertSame($expected, $actual);
    }

    protected function mockContextPath()
    {
        $theme = $this->getMockForAbstractClass('\Magento\View\Design\ThemeInterface');
        $this->design->expects($this->once())
            ->method('getArea')
            ->will($this->returnValue('area'));
        $this->design->expects($this->once())
            ->method('getDesignTheme')
            ->will($this->returnValue($theme));
        $this->design->expects($this->once())
            ->method('getLocale')
            ->will($this->returnValue('locale'));
        $this->path->expects($this->once())
            ->method('getPathUsingTheme')
            ->with('area', $theme, 'locale')
            ->will($this->returnValue('area/theme/locale'));
    }
}
