<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\FileResolution\Fallback;

class ViewFileTest extends \PHPUnit_Framework_TestCase
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
     * @var ViewFile
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
            array('createViewFileRule'),
            array($this->getMock('Magento\App\Filesystem', array(), array(), '', false))
        );
        $this->rule = $this->getMockForAbstractClass('Magento\View\Design\Fallback\Rule\RuleInterface');
        $this->fallbackFactory
            ->expects($this->any())->method('createViewFileRule')->will($this->returnValue($this->rule));

        $this->resolver = $this->getMock('\Magento\View\Design\FileResolution\Fallback\Resolver');

        $this->theme = $this->getMock('Magento\View\Design\ThemeInterface', array(), array(), '', false);
        $this->theme->expects($this->any())
            ->method('getThemePath')
            ->will($this->returnValue('magento_theme'));

        $this->cache = $this->getMockForAbstractClass('Magento\View\Design\FileResolution\Fallback\CacheDataInterface');

        $this->fallback = new ViewFile(
            $this->cache, $filesystem, $this->fallbackFactory, $this->resolver, ['css' => ['less']]
        );
    }

    /**
     * @param array $additionalExtensions
     *
     * @dataProvider constructorExceptionDataProvider
     */
    public function testConstructorException(array $additionalExtensions)
    {
        $this->setExpectedException('\InvalidArgumentException', "\$staticExtensionRule must be an array with format: "
            . "array('ext1' => array('ext1', 'ext2'), 'ext3' => array(...)]");

        $filesystem = $this->getMock('Magento\App\Filesystem', array(), array(), '', false);
        $this->fallback = new ViewFile(
            $this->cache, $filesystem, $this->fallbackFactory, $this->resolver, $additionalExtensions
        );
    }

    /**
     * @return array
     */
    public function constructorExceptionDataProvider()
    {
        return [
            'numerical keys'   => [['css', 'less']],
            'non-array values' => [['css' => 'less']],
        ];
    }

    /**
     * @dataProvider getViewFileDataProvider
     */
    public function testGetViewFile(
        $fullModuleName, $namespace, $module, $targetFile, $expectedFileName, $expectedCacheId
    ) {
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

        $params = array('area' => 'area', 'theme' => $this->theme, 'namespace' => $namespace, 'module' => $module,
            'locale' => 'locale');

        $this->resolver->expects($this->once())
            ->method('resolveFile')
            ->with($this->directoryMock, $this->rule, $requestedFile, $params)
            ->will($this->returnValue($expectedFileName));

        $this->cache->expects($this->once())
            ->method('saveToCache')
            ->with($expectedFileName, 'view', $requestedFile, $params)
            ->will($this->returnValue(true));

        $filename = $this->fallback->getViewFile('area', $this->theme, 'locale', 'file.txt', $fullModuleName);

        $this->assertSame($expectedFileName, $filename);
    }

    /**
     * @return array
     */
    public function getViewFileDataProvider()
    {
        return array(
            'no module, file found' => array(
                null,
                null,
                null,
                'found_folder/file.txt',
                'found_folder/file.txt',
                'type:view|area:area|theme:magento_theme|locale:locale|module:_|file:file.txt',
            ),
            'module, file found' => array(
                'Namespace_Module',
                'Namespace',
                'Module',
                'found_folder/file.txt',
                'found_folder/file.txt',
                'type:view|area:area|theme:magento_theme|locale:locale|module:Namespace_Module|file:file.txt',
            ),
            'no module, file not found' => array(
                null,
                null,
                null,
                null,
                false,
                'type:view|area:area|theme:magento_theme|locale:locale|module:_|file:file.txt',
            ),
            'module, file not found' => array(
                'Namespace_Module',
                'Namespace',
                'Module',
                null,
                false,
                'type:view|area:area|theme:magento_theme|locale:locale|module:Namespace_Module|file:file.txt',
            ),
        );
    }

    public function testGetViewFileAdditionalExtension()
    {
        $targetFile = 'found_folder/file.less';
        $requestedCssFile = 'file.css';
        $requestedLessFile = 'file.less';
        $expectedFile = 'found_folder/file.less';

        $this->directoryMock->expects($this->any())
            ->method('isExist')
            ->will(
                $this->returnCallback(
                    function ($tryFile) use ($targetFile) {
                        return ($tryFile == $targetFile);
                    }
                )
            );

        $params = array('area' => 'area', 'theme' => $this->theme, 'namespace' => 'Namespace', 'module' => 'Module',
            'locale' => 'locale');

        $this->resolver->expects($this->at(0))
            ->method('resolveFile')
            ->with($this->directoryMock, $this->rule, $requestedCssFile, $params)
            ->will($this->returnValue(false));
        $this->resolver->expects($this->at(1))
            ->method('resolveFile')
            ->with($this->directoryMock, $this->rule, $requestedLessFile, $params)
            ->will($this->returnValue($expectedFile));

        $filename = $this->fallback->getViewFile('area', $this->theme, 'locale', $requestedCssFile, 'Namespace_Module');

        $this->assertSame($expectedFile, $filename);
    }

    public function testGetViewFileCached()
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

        $params = array(
            'area' => 'frontend',
            'theme' => $this->theme,
            'locale' => 'en_US',
            'namespace' => 'Magento',
            'module' => 'Core',
        );

        $this->cache->expects($this->once())
            ->method('getFromCache')
            ->with('view', $file, $params)
            ->will($this->returnValue($expectedResult));

        $resultCached = $this->fallback->getViewFile('frontend', $this->theme, 'en_US', $file, 'Magento_Core');
        $this->assertSame($expectedResult, $resultCached);
    }
}
