<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\App;

class BootstrapTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\AppInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $application;

    /**
     * @var \Magento\Framework\App\ObjectManagerFactory | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerFactory;

    /**
     * @var \Magento\Framework\ObjectManager\ObjectManager | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\Logger | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $logger;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $dirs;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configDir;

    /**
     * @var MaintenanceMode | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $maintenanceMode;

    /**
     * @var \Magento\Framework\App\Bootstrap | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $bootstrapMock;

    public function setUp()
    {
        $this->objectManagerFactory = $this->getMock('\Magento\Framework\App\ObjectManagerFactory', [], [], '', false);
        $this->objectManager = $this->getMockForAbstractClass('\Magento\Framework\ObjectManager');
        $this->dirs = $this->getMock('\Magento\Framework\App\Filesystem\DirectoryList', ['getDir'], [], '', false);
        $this->maintenanceMode = $this->getMock('\Magento\Framework\App\MaintenanceMode', ['isOn'], [], '', false);
        $filesystem = $this->getMock('Magento\Framework\App\Filesystem', [], [], '', false);

        $this->logger = $this->getMock('Magento\Framework\Logger', [], [], '', false);

        $mapObjectManager = [
            ['Magento\Framework\App\Filesystem\DirectoryList', $this->dirs],
            ['Magento\Framework\App\MaintenanceMode', $this->maintenanceMode],
            ['Magento\Framework\App\Filesystem', $filesystem],
            ['Magento\Framework\Logger', $this->logger]
        ];

        $this->objectManager->expects($this->any())->method('get')
            ->will(($this->returnValueMap($mapObjectManager)));

        $this->configDir = $this->getMockForAbstractClass('\Magento\Framework\Filesystem\Directory\ReadInterface');

        $filesystem->expects($this->any())->method('getDirectoryRead')
            ->will(($this->returnValue($this->configDir)));

        $this->application = $this->getMockForAbstractClass('\Magento\Framework\AppInterface');

        $this->objectManager->expects($this->any())->method('create')
            ->will(($this->returnValue($this->application)));

        $this->objectManagerFactory->expects($this->any())->method('create')
            ->will(($this->returnValue($this->objectManager)));

        $this->bootstrapMock = $this->getMock('\Magento\Framework\App\Bootstrap',
            ['assertMaintenance', 'assertInstalled', 'getIsExpected', 'isInstalled', 'terminate'],
            [$this->objectManagerFactory, '', ['value1', 'value2']]
        );
    }

    public function testGetParams()
    {
        $testParams = ['testValue1', 'testValue2'];
        $bootstrap = self::createBootstrap($testParams);
        $this->assertSame($testParams, $bootstrap->getParams());
    }

    /**
     * Creates a bootstrap object
     *
     * @param array $testParams
     * @return Bootstrap
     */
    private function createBootstrap($testParams = ['value1', 'value2'])
    {
        return new Bootstrap($this->objectManagerFactory, '', $testParams);
    }

    public function testCreateApplication()
    {
        $bootstrap = self::createBootstrap();
        $testArgs = ['arg1', 'arg2'];
        $this->assertSame($this->application, $bootstrap->createApplication('someApplicationType', $testArgs));
    }

    public function testGetObjectManager()
    {
        $bootstrap = self::createBootstrap();
        $this->assertSame($this->objectManager, $bootstrap->getObjectManager());
    }

    public function testGetDirList()
    {
        $bootstrap = self::createBootstrap();
        $this->assertSame($this->dirs, $bootstrap->getDirList());
    }

    public function testIsDeveloperMode()
    {
        $bootstrap = self::createBootstrap();
        $this->assertFalse($bootstrap->isDeveloperMode());
        $testParams = [State::PARAM_MODE => State::MODE_DEVELOPER];
        $bootstrap = self::createBootstrap($testParams);
        $this->assertTrue($bootstrap->isDeveloperMode());
    }

    public function testRunNoErrors()
    {
        $responseMock = $this->getMockForAbstractClass('\Magento\Framework\App\ResponseInterface');
        $this->bootstrapMock->expects($this->once())->method('assertMaintenance')->will($this->returnValue(null));
        $this->bootstrapMock->expects($this->once())->method('assertInstalled')->will($this->returnValue(null));
        $this->application->expects($this->once())->method('launch')->willReturn($responseMock);
        $this->bootstrapMock->run($this->application);
    }

    public function testRunWithMaintenanceErrors()
    {
        $expectedException = new \Exception('Unable to proceed: the maintenance mode is enabled.', 0, null);
        $this->bootstrapMock->expects($this->once())->method('assertMaintenance')
            ->will($this->throwException($expectedException));
        $this->bootstrapMock->expects($this->once())->method('terminate')->with($expectedException);
        $this->application->expects($this->once())->method('catchException')->willReturn(false);
        $this->bootstrapMock->run($this->application);
    }

    public function testRunWithInstallErrors()
    {
        $expectedException = new \Exception('Application is not installed yet');
        $this->bootstrapMock->expects($this->once())->method('assertMaintenance')->will($this->returnValue(null));
        $this->bootstrapMock->expects($this->once())->method('assertInstalled')
            ->will($this->throwException($expectedException));
        $this->bootstrapMock->expects($this->once())->method('terminate')->with($expectedException);
        $this->application->expects($this->once())->method('catchException')->willReturn(false);
        $this->bootstrapMock->run($this->application);
    }

    public function testRunWithBothErrors()
    {
        $expectedMaintenanceException = new \Exception('Unable to proceed: the maintenance mode is enabled.');
        $this->bootstrapMock->expects($this->once())->method('assertMaintenance')
            ->will($this->throwException($expectedMaintenanceException));
        $this->bootstrapMock->expects($this->never())->method('assertInstalled');
        $this->bootstrapMock->expects($this->once())->method('terminate')->with($expectedMaintenanceException);
        $this->application->expects($this->once())->method('catchException')->willReturn(false);
        $this->bootstrapMock->run($this->application);
    }

    /**
     * @dataProvider assertMaintenanceDataProvider
     */
    public function testAssertMaintenance($isOn, $isExpected)
    {
        $bootstrap = self::createBootstrap([Bootstrap::PARAM_REQUIRE_MAINTENANCE => $isExpected]);


        $this->maintenanceMode->expects($this->once())->method('isOn')->willReturn($isOn);
        $this->application->expects($this->never())->method('launch');
        $this->application->expects($this->once())->method('catchException')->willReturn(true);
        $bootstrap->run($this->application);
        $this->assertEquals($bootstrap->getErrorCode(), Bootstrap::ERR_MAINTENANCE);

    }

    public function assertMaintenanceDataProvider()
    {
        return [
            [true, false],
            [false, true]
        ];
    }

    /**
     * @dataProvider assertInstalledDataProvider
     */
    public function testAssertInstalled($isInstalled, $isExpected)
    {
        $bootstrap = self::createBootstrap([Bootstrap::PARAM_REQUIRE_IS_INSTALLED => $isExpected]);
        $this->configDir->expects($this->once())->method('isExist')->willReturn($isInstalled);
        $this->application->expects($this->never())->method('launch');
        $this->application->expects($this->once())->method('catchException')->willReturn(true);
        $bootstrap->run($this->application);
        $this->assertEquals($bootstrap->getErrorCode(), Bootstrap::ERR_IS_INSTALLED);
    }

    public function assertInstalledDataProvider()
    {
        return [
            [false, true],
            [true, false],
        ];
    }
}
