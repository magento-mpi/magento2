<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\FileResolution\Fallback;

class FileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Design\Fallback\Rule\RuleInterface
     */
    protected $rule;

    /**
     * @var \Magento\View\Design\Fallback\Factory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $fallbackFactory;

    /**
     * @var \Magento\Filesystem\Directory\Read|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $directoryMock;

    /**
     * @var \Magento\View\Design\FileResolution\Fallback\Resolver|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resolver;

    /**
     * @var \Magento\View\Design\ThemeInterface
     */
    protected $theme;

    /**
     * @var \Magento\View\Design\FileResolution\Fallback\CacheDataInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cache;

    /**
     * @var File
     */
    protected $fallback;

    protected function setUp()
    {
        $this->directoryMock = $this->getMock(
            'Magento\Filesystem\Directory\Read',
            array('isExist', 'getRelativePath', 'getAbsolutePath'), array(), '', false
        );
        $this->directoryMock->expects($this->any())
            ->method('getRelativePath')
            ->will($this->returnArgument(0));
        $this->directoryMock->expects($this->any())
            ->method('getAbsolutePath')
            ->will($this->returnArgument(0));
        $filesystem = $this->getMock('Magento\App\Filesystem', array(), array(), '', false);
        $filesystem->expects($this->once())
            ->method('getDirectoryRead')
            ->with(\Magento\App\Filesystem::ROOT_DIR)
            ->will($this->returnValue($this->directoryMock));

        $this->fallbackFactory = $this->getMock(
            'Magento\View\Design\Fallback\Factory',
            array('createFileRule'),
            array($this->getMock('Magento\App\Filesystem', array(), array(), '', false))
        );
        $this->rule = $this->getMockForAbstractClass('Magento\View\Design\Fallback\Rule\RuleInterface');
        $this->fallbackFactory
            ->expects($this->any())->method('createFileRule')->will($this->returnValue($this->rule));

        $this->resolver = $this->getMock('\Magento\View\Design\FileResolution\Fallback\Resolver');

        $this->theme = $this->getMock('Magento\View\Design\ThemeInterface', array(), array(), '', false);
        $this->theme->expects($this->any())
            ->method('getThemePath')
            ->will($this->returnValue('magento_theme'));

        $this->cache = $this->getMockForAbstractClass('Magento\View\Design\FileResolution\Fallback\CacheDataInterface');

        $this->fallback = new File($this->cache, $filesystem, $this->fallbackFactory, $this->resolver);
    }

    /**
     * @dataProvider getFileDataProvider
     */
    public function testGetFile($fullModuleName, $namespace, $module, $targetFile, $expectedFileName)
    {
        $requestedFile = 'file.txt';

        $this->directoryMock->expects($this->any())
            ->method('isExist')
            ->will(
                $this->returnCallback(
                    function ($tryFile) use ($targetFile) {
                        return ($tryFile == $targetFile);
                    }
                )
            );

        $params = array('area' => 'area', 'theme' => $this->theme, 'namespace' => $namespace, 'module' => $module);

        $this->resolver->expects($this->once())
            ->method('resolveFile')
            ->with($this->directoryMock, $this->rule, $requestedFile, $params)
            ->will($this->returnValue($expectedFileName));

        $this->cache->expects($this->any())
            ->method('getFromCache')
            ->will($this->returnValue(false));
        $this->cache->expects($this->once())
            ->method('saveToCache')
            ->with($expectedFileName, 'file', $requestedFile, $params)
            ->will($this->returnValue(true));

        $filename = $this->fallback->getFile('area', $this->theme, $requestedFile, $fullModuleName);

        $this->assertSame($expectedFileName, $filename);
    }

    /**
     * @return array
     */
    public function getFileDataProvider()
    {
        return array(
            'no module, file found' => array(
                null,
                null,
                null,
                'found_folder/file.txt',
                'found_folder/file.txt',
            ),
            'module, file found' => array(
                'Namespace_Module',
                'Namespace',
                'Module',
                'found_folder/file.txt',
                'found_folder/file.txt',
            ),
            'no module, file not found' => array(
                null,
                null,
                null,
                null,
                false,
            ),
            'module, file not found' => array(
                'Namespace_Module',
                'Namespace',
                'Module',
                null,
                false,
            ),
        );
    }

    public function testGetFileCached()
    {
        $file = 'some/file';
        $expectedResult = 'some/file';

        $this->directoryMock->expects($this->any())
            ->method('isExist')
            ->will(
                $this->returnCallback(
                    function ($tryFile) use ($file) {
                        return ($tryFile == $file);
                    }
                )
            );

        $params = array('area' => 'frontend', 'theme' => $this->theme, 'namespace' => 'Magento', 'module' => 'Core');
        $this->cache->expects($this->once())
            ->method('getFromCache')
            ->with('file', $file, $params)
            ->will($this->returnValue($expectedResult));

        $actual = $this->fallback->getFile('frontend', $this->theme, $file, 'Magento_Core');
        $this->assertSame($expectedResult, $actual);
    }
}
