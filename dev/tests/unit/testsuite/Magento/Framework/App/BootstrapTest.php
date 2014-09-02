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
     * @var \Magento\Framework\App\Bootstrap | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $bootstrap;

    /**
     * @var \Magento\Framework\Application | \PHPUnit_Framework_MockObject_MockObject
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
     * @var Magento\Framework\Logger | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $loggerMock;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $dirs;

    /**
     * @var MaintenanceMode | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $maintenanceModeMock;

    /**
     * @var array
     */
    protected $testArgs;

    /**
     * @var array
     */
    protected $testParams;

    public function setUp()
    {

        $this->testParams = ['value1', 'value2'];
        $this->testArgs = ['arg1', 'arg2'];

        $this->objectManagerFactory = $this->getMock('\Magento\Framework\App\ObjectManagerFactory', [], [], '', false);
        $this->objectManager = $this->getMockForAbstractClass('\Magento\Framework\ObjectManager');
        $this->dirs = $this->getMock('Magento\Framework\App\Filesystem\DirectoryList', ['getDir'], [], '', false);
        $this->maintenanceModeMock = $this->getMock('Magento\Framework\App\MaintenanceMode', ['isOn'], [], '', false);
        $this->loggerMock = $this->getMock('Magento\Framework\Logger', [], [], '', false);
    }

    public function tearDown()
    {
        unset($this->objectManagerFactory);
        unset($this->objectManager);
        unset($this->dirs);
        unset($this->maintenanceModeMock);

    }

    public function testGetParams()
    {
        $this->bootstrap = new Bootstrap($this->objectManagerFactory, '', $this->testParams);
        $this->assertSame($this->testParams, $this->bootstrap->getParams());
    }

    private function initBootstrapTest()
    {
        $mapObjectManager = [
            ['Magento\Framework\App\Filesystem\DirectoryList', $this->dirs],
            ['Magento\Framework\App\MaintenanceMode', $this->maintenanceModeMock],
            ['Magento\Framework\Logger', $this->loggerMock]
        ];

        $this->objectManager->expects($this->any())->method('get')
            ->will(($this->returnValueMap($mapObjectManager)));

        $this->application = $this->getMockForAbstractClass('\Magento\Framework\AppInterface');

        $this->objectManager->expects($this->any())->method('create')
            ->will(($this->returnValue($this->application)));

        $this->objectManagerFactory->expects($this->any())->method('create')
            ->will(($this->returnValue($this->objectManager)));

        $this->bootstrap = new Bootstrap($this->objectManagerFactory, '', $this->testParams);
    }

    public function testCreateApplication()
    {
        self::initBootstrapTest();
        $this->assertSame($this->application,
            $this->bootstrap->createApplication('someApplicationType', $this->testArgs));
    }

    public function testGetObjectManager()
    {
        self::initBootstrapTest();
        $this->assertSame($this->objectManager, $this->bootstrap->getObjectManager());
    }

    public function testGetDirList()
    {
        self::initBootstrapTest();
        $this->assertSame($this->dirs, $this->bootstrap->getDirList());
    }

    public function testIsDeveloperMode()
    {
        self::initBootstrapTest();
        $this->assertFalse($this->bootstrap->isDeveloperMode());
        $this->testParams = [State::PARAM_MODE => State::MODE_DEVELOPER];
        self::initBootstrapTest();
        $this->assertTrue($this->bootstrap->isDeveloperMode());
    }

    /**
     * @param String $params
     * @param bool $maintenanceMode
     * @param bool $installMode
     * @param int $errorCode
     *
     * @dataProvider maintenanceModeAndRunDataProvider
     */
    public function testRunErrors($params, $maintenanceMode, $installMode, $errorCode)
    {
        $this->testParams = [$params];
        self::initBootstrapTest();
        $this->maintenanceModeMock->expects($this->any())->method('isOn')->willReturn($maintenanceMode);
        $this->dirs->expects($this->any())->method('getDir')->willReturn($installMode);
        $this->application->expects($this->any())->method('catchException')->willReturn(true);
        $this->bootstrap->run($this->application);
        $this->assertEquals($errorCode, (int)$this->bootstrap->getErrorCode());
    }

    /**
     * Data provider for testMaintenanceMode
     *
     * @return array
     */
    public function maintenanceModeAndRunDataProvider()
    {
        $ternaryCases = [true, false, null];
        $binaryCases = [true, false];
        $returnArray = [];

        foreach ($ternaryCases as $maintenanceParam) {
            foreach ($ternaryCases as $installParam) {
                foreach ($binaryCases as $isOn) {
                    foreach ($binaryCases as $isInstalled) {
                        $tempArray = [[Bootstrap::PARAM_REQUIRE_MAINTENANCE => $maintenanceParam,
                            Bootstrap::PARAM_REQUIRE_IS_INSTALLED => $installParam],
                            $isOn,
                            $isInstalled,
                            $isOn ? 901:902];
                        array_push($returnArray, $tempArray);
                    }
                }
            }
        }
        return $returnArray;
    }
}
