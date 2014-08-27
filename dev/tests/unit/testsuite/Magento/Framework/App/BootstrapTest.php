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
    protected $logger;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $dirs;

    /**
     * @var MaintenanceMode | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $maintenanceMode;

    public function setUp()
    {
        $this->objectManagerFactory = $this->getMock('\Magento\Framework\App\ObjectManagerFactory', [], [], '', false);
        $this->objectManager = $this->getMockForAbstractClass('\Magento\Framework\ObjectManager');
        $this->dirs = $this->getMock('Magento\Framework\App\Filesystem\DirectoryList', ['getDir'], [], '', false);
        $this->maintenanceMode = $this->getMock('Magento\Framework\App\MaintenanceMode', ['isOn'], [], '', false);
        $this->logger = $this->getMock('Magento\Framework\Logger', [], [], '', false);
    }

    public function testGetParams()
    {
        $testParams = ['testValue1', 'testValue2'];
        $bootstrap = self::createBootstrap($testParams);
        $this->assertSame($testParams, $bootstrap->getParams());
    }

    /**
     * Creates a boostrap object
     *
     * @param array $testParams
     * @return Bootstrap
     */
    private function createBootstrap($testParams = ['value1', 'value2'])
    {
        $mapObjectManager = [
            ['Magento\Framework\App\Filesystem\DirectoryList', $this->dirs],
            ['Magento\Framework\App\MaintenanceMode', $this->maintenanceMode],
            ['Magento\Framework\Logger', $this->logger]
        ];

        $this->objectManager->expects($this->any())->method('get')
            ->will(($this->returnValueMap($mapObjectManager)));

        $this->application = $this->getMockForAbstractClass('\Magento\Framework\AppInterface');

        $this->objectManager->expects($this->any())->method('create')
            ->will(($this->returnValue($this->application)));

        $this->objectManagerFactory->expects($this->any())->method('create')
            ->will(($this->returnValue($this->objectManager)));

        $bootstrap = new Bootstrap($this->objectManagerFactory, '', $testParams);
        return($bootstrap);
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

    /**
     * @param String $params
     * @param bool $maintenanceMode
     * @param bool $installMode
     * @param int $errorCode
     *
     * @dataProvider testRunErrorsProvider
     */
    public function testRunErrors($params, $maintenanceMode, $installMode, $errorCode)
    {
        $bootstrap = self::createBootstrap([$params]);
        $this->maintenanceMode->expects($this->any())->method('isOn')->willReturn($maintenanceMode);
        $this->dirs->expects($this->any())->method('getDir')->willReturn($installMode);
        $this->application->expects($this->any())->method('catchException')->willReturn(true);
        $bootstrap->run($this->application);
        $this->assertEquals($errorCode, (int)$bootstrap->getErrorCode());
    }

    /**
     * Data provider for testRunErrors
     *
     * @return array
     */
    public function testRunErrorsProvider()
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
