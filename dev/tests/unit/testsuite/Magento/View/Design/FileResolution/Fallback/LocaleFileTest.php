<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\FileResolution\Fallback;

class LocaleFileTest extends \PHPUnit_Framework_TestCase
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
     * @var LocaleFile
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

        $this->rule = $this->getMockForAbstractClass('Magento\View\Design\Fallback\Rule\RuleInterface');

        $this->fallbackFactory = $this->getMock(
            'Magento\View\Design\Fallback\Factory',
            array('createLocaleFileRule'),
            array($this->getMock('Magento\App\Filesystem', array(), array(), '', false))
        );
        $this->fallbackFactory
            ->expects($this->any())->method('createLocaleFileRule')->will($this->returnValue($this->rule));

        $this->resolver = $this->getMock('\Magento\View\Design\FileResolution\Fallback\Resolver');

        $this->theme = $this->getMock('Magento\View\Design\ThemeInterface', array(), array(), '', false);
        $this->theme->expects($this->any())
            ->method('getThemePath')
            ->will($this->returnValue('magento_theme'));

        $this->cache = $this->getMockForAbstractClass('Magento\View\Design\FileResolution\Fallback\CacheDataInterface');

        $this->fallback = new LocaleFile($this->cache, $filesystem, $this->fallbackFactory, $this->resolver);
    }

    /**
     * @dataProvider getLocaleFileDataProvider
     */
    public function testGetLocaleFile($targetFile, $expectedFileName, $expectedCacheId)
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

        $params = array('area' => 'area', 'theme' => $this->theme, 'locale' => 'locale');

        $this->resolver->expects($this->once())
            ->method('resolveFile')
            ->with($this->directoryMock, $this->rule, $requestedFile, $params)
            ->will($this->returnValue($expectedFileName));

        $this->cache->expects($this->once())
            ->method('saveToCache')
            ->with($expectedFileName, 'locale', $requestedFile, $params)
            ->will($this->returnValue(true));

        $filename = $this->fallback->getLocaleFile('area', $this->theme, 'locale', $requestedFile);

        $this->assertSame($expectedFileName, $filename);
    }

    /**
     * @return array
     */
    public function getLocaleFileDataProvider()
    {
        return array(
            'file found' => array(
                'found_folder/file.txt',
                'found_folder/file.txt',
                'type:locale|area:area|theme:magento_theme|locale:locale|module:_|file:file.txt',
            ),
            'file not found' => array(
                null,
                false,
                'type:locale|area:area|theme:magento_theme|locale:locale|module:_|file:file.txt',
            )
        );
    }

    public function testGetLocaleFileCached()
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

        $params = array('area' => 'frontend', 'theme' => $this->theme, 'locale' => 'en_US');

        $this->cache->expects($this->once())
            ->method('getFromCache')
            ->with('locale', $file, $params)
            ->will($this->returnValue($expectedResult));

        $resultCached = $this->fallback->getLocaleFile('frontend', $this->theme, 'en_US', $file);
        $this->assertSame($expectedResult, $resultCached);
    }
}
