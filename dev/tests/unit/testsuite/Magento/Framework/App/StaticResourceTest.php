<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App;

class StaticResourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\State|\PHPUnit_Framework_MockObject_MockObject
     */
    private $state;

    /**
     * @var \Magento\Framework\App\Response\FileInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $response;

    /**
     * @var \Magento\Framework\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    private $request;

    /**
     * @var \Magento\Framework\App\View\Asset\Publisher|\PHPUnit_Framework_MockObject_MockObject
     */
    private $publisher;

    /**
     * @var \Magento\Framework\View\Asset\Repository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $assetRepo;

    /**
     * @var \Magento\Framework\Module\ModuleList|\PHPUnit_Framework_MockObject_MockObject
     */
    private $moduleList;

    /**
     * @var \Magento\Framework\ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $objectManager;

    /**
     * @var \Magento\Framework\App\ObjectManager\ConfigLoader|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configLoader;

    /**
     * @var \Magento\Framework\App\StaticResource
     */
    private $object;

    protected function setUp()
    {
        $this->state = $this->getMock('Magento\Framework\App\State', array(), array(), '', false);
        $this->response = $this->getMock('Magento\Core\Model\File\Storage\Response', array(), array(), '', false);
        $this->request = $this->getMock('Magento\Framework\App\Request\Http', array(), array(), '', false);
        $this->publisher = $this->getMock('Magento\Framework\App\View\Asset\Publisher', array(), array(), '', false);
        $this->assetRepo = $this->getMock('Magento\Framework\View\Asset\Repository', array(), array(), '', false);
        $this->moduleList = $this->getMock('Magento\Framework\Module\ModuleList', array(), array(), '', false);
        $this->objectManager = $this->getMockForAbstractClass('Magento\Framework\ObjectManagerInterface');
        $this->configLoader = $this->getMock(
            'Magento\Framework\App\ObjectManager\ConfigLoader', array(), array(), '', false
        );
        $this->object = new \Magento\Framework\App\StaticResource(
            $this->state,
            $this->response,
            $this->request,
            $this->publisher,
            $this->assetRepo,
            $this->moduleList,
            $this->objectManager,
            $this->configLoader,
            $this->getMockForAbstractClass('\Magento\Framework\View\DesignInterface')
        );
    }

    public function testLaunchProductionMode()
    {
        $this->state->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue(\Magento\Framework\App\State::MODE_PRODUCTION));
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
     * @param string $requestedModule
     * @param bool $moduleExists
     * @param string $expectedFile
     * @param array $expectedParams
     *
     * @dataProvider launchDataProvider
     */
    public function testLaunch(
        $mode,
        $requestedPath,
        $requestedModule,
        $moduleExists,
        $expectedFile,
        array $expectedParams
    ) {
        $this->state->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue($mode));
        $this->state->expects($this->once())
            ->method('setAreaCode')
            ->with('area');
        $this->configLoader->expects($this->once())
            ->method('load')
            ->with('area')
            ->will($this->returnValue(array('config')));
        $this->objectManager->expects($this->once())
            ->method('configure')
            ->with(array('config'));
        $this->request->expects($this->once())
            ->method('get')
            ->with('resource')
            ->will($this->returnValue($requestedPath));
        $this->moduleList->expects($this->any())
            ->method('has')
            ->with($requestedModule)
            ->will($this->returnValue($moduleExists));
        $asset = $this->getMockForAbstractClass('\Magento\Framework\View\Asset\LocalInterface');
        $asset->expects($this->once())->method('getSourceFile')->will($this->returnValue('resource/file.css'));
        $this->assetRepo->expects($this->once())
            ->method('createAsset')
            ->with($expectedFile, $expectedParams)
            ->will($this->returnValue($asset));
        $this->publisher->expects($this->once())->method('publish')->with($asset);
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
                \Magento\Framework\App\State::MODE_DEVELOPER,
                'area/Magento/theme/locale/dir/file.js',
                'dir',
                false,
                'dir/file.js',
                array('area' => 'area', 'locale' => 'locale', 'module' => '', 'theme' => 'Magento/theme'),
            ),
            'default mode with modular resource' => array(
                \Magento\Framework\App\State::MODE_DEFAULT,
                'area/Magento/theme/locale/Namespace_Module/dir/file.js',
                'Namespace_Module',
                true,
                'dir/file.js',
                array(
                    'area' => 'area', 'locale' => 'locale', 'module' => 'Namespace_Module', 'theme' => 'Magento/theme'
                ),
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
            ->will($this->returnValue(\Magento\Framework\App\State::MODE_DEVELOPER));
        $this->request->expects($this->once())
            ->method('get')
            ->with('resource')
            ->will($this->returnValue('short/path.js'));
        $this->object->launch();
    }

    public function testCatchException()
    {
        $bootstrap = $this->getMock('Magento\Framework\App\Bootstrap', [], [], '', false);
        $bootstrap->expects($this->at(0))->method('isDeveloperMode')->willReturn(false);
        $bootstrap->expects($this->at(1))->method('isDeveloperMode')->willReturn(true);
        $exception = new \Exception('message');
        $this->response->expects($this->exactly(2))->method('setHttpResponseCode')->with(404);
        $this->response->expects($this->exactly(2))->method('setHeader')->with('Content-Type', 'text/plain');
        $this->response->expects($this->exactly(2))->method('sendResponse');
        $this->response->expects($this->once())->method('setBody')->with($this->stringStartsWith('message'));
        $this->assertTrue($this->object->catchException($bootstrap, $exception));
        $this->assertTrue($this->object->catchException($bootstrap, $exception));
    }
}
