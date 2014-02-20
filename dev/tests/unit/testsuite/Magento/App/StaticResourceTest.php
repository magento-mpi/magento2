<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

class StaticResourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\State|\PHPUnit_Framework_MockObject_MockObject
     */
    private $state;

    /**
     * @var \Magento\App\Response\FileInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $response;

    /**
     * @var \Magento\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    private $request;

    /**
     * @var \Magento\View\FileResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $fileResolver;

    /**
     * @var \Magento\Module\ModuleList|\PHPUnit_Framework_MockObject_MockObject
     */
    private $moduleList;

    /**
     * @var \Magento\View\Design\Theme\ListInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $themeList;

    /**
     * @var \Magento\App\StaticResource
     */
    private $object;

    protected function setUp()
    {
        $this->state = $this->getMock('Magento\App\State', array(), array(), '', false);
        $this->response = $this->getMockForAbstractClass('Magento\App\Response\FileInterface');
        $this->request = $this->getMock('Magento\App\Request\Http', array(), array(), '', false);
        $this->fileResolver = $this->getMock('Magento\View\FileResolver', array(), array(), '', false);
        $this->moduleList = $this->getMock('Magento\Module\ModuleList', array(), array(), '', false);
        $this->themeList = $this->getMockForAbstractClass('Magento\View\Design\Theme\ListInterface');
        $this->object = new \Magento\App\StaticResource(
            $this->state,
            $this->response,
            $this->request,
            $this->fileResolver,
            $this->moduleList,
            $this->themeList
        );
    }

    public function testLaunchProductionMode()
    {
        $this->state->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue(\Magento\App\State::MODE_PRODUCTION));
        $this->response->expects($this->once())
            ->method('setHttpResponseCode')
            ->with(404);
        $this->response->expects($this->never())
            ->method('setFilePath');
        $this->object->launch();
    }

    /**
     * @param string $mode
     * @param string $requestedPath
     * @param string $expectedThemePath
     * @param string $expectedMethod
     * @param string $expectedModule
     * @param bool $moduleExists
     * @param string $expectedFile
     * @param array $expectedParams
     *
     * @dataProvider launchDataProvider
     */
    public function testLaunch(
        $mode,
        $requestedPath,
        $expectedThemePath,
        $expectedMethod,
        $expectedModule,
        $moduleExists,
        $expectedFile,
        array $expectedParams
    ) {
        $this->state->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue($mode));
        $this->request->expects($this->once())
            ->method('get')
            ->with('resource')
            ->will($this->returnValue($requestedPath));
        $theme = $this->getMockForAbstractClass('Magento\View\Design\ThemeInterface');
        $expectedParams['themeModel'] = $theme;
        $this->themeList->expects($this->once())
            ->method('getThemeByFullPath')
            ->with($expectedThemePath)
            ->will($this->returnValue($theme));
        $this->moduleList->expects($this->any())
            ->method('getModule')
            ->with($expectedModule)
            ->will($this->returnValue($moduleExists));
        $this->fileResolver->expects($this->once())
            ->method($expectedMethod)
            ->with($expectedFile, $expectedParams)
            ->will($this->returnValue('resource/file.css'));
        $this->response->expects($this->once())
            ->method('setFilePath')
            ->with('resource/file.css');
        $this->object->launch();
    }

    /**
     * @return array
     */
    public function launchDataProvider()
    {
        return array(
            'developer mode with non-modular resource' => array(
                \Magento\App\State::MODE_DEVELOPER,
                'area/theme/locale/dir/file.js',
                'area/theme',
                'getViewFile',
                'dir',
                null,
                'dir/file.js',
                array('area' => 'area', 'locale' => 'locale', 'module' => ''),
            ),
            'default mode with modular resource' => array(
                \Magento\App\State::MODE_DEFAULT,
                'area/theme/locale/Namespace_Module/dir/file.js',
                'area/theme',
                'getPublicViewFile',
                'Namespace_Module',
                true,
                'dir/file.js',
                array('area' => 'area', 'locale' => 'locale', 'module' => 'Namespace_Module'),
            ),
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Requested path 'short/path.js' is wrong
     */
    public function testLaunchWrongPath()
    {
        $this->state->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue(\Magento\App\State::MODE_DEVELOPER));
        $this->request->expects($this->once())
            ->method('get')
            ->with('resource')
            ->will($this->returnValue('short/path.js'));
        $this->object->launch();
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Can't find theme 'nonexistent_theme' for area 'area'
     */
    public function testLaunchNonexistentTheme()
    {
        $this->state->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue(\Magento\App\State::MODE_DEVELOPER));
        $this->request->expects($this->once())
            ->method('get')
            ->with('resource')
            ->will($this->returnValue('area/nonexistent_theme/dir/file.js'));
        $this->themeList->expects($this->once())
            ->method('getThemeByFullPath')
            ->with('area/nonexistent_theme')
            ->will($this->returnValue(null));
        $this->object->launch();
    }
}
