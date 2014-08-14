<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Performance;

class BootstrapTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $appBootstrap;

    protected function setUp()
    {
        $this->appBootstrap = $this->getMock('Magento\Framework\App\Bootstrap', [], [], '', false);
        $dirList = $this->getMock('Magento\Framework\App\Filesystem\DirectoryList', [], [], '', false);
        $dirList->expects($this->any())->method('getRoot')->will($this->returnValue('something'));
        $this->appBootstrap->expects($this->any())->method('getDirList')->will($this->returnValue($dirList));
        $objectManager = $this->getMockForAbstractClass('Magento\Framework\ObjectManager');
        $this->appBootstrap->expects($this->any())
            ->method('getObjectManager')
            ->will($this->returnValue($objectManager));
    }

    protected function tearDown()
    {
        // Delete a directory, where tests do some temporary work
        $tmpDir = $this->_getBaseFixtureDir() . '/config_dist/tmp';
        $filesystemAdapter = new \Magento\Framework\Filesystem\Driver\File();
        if ($filesystemAdapter->isExists($tmpDir)) {
            $filesystemAdapter->deleteDirectory($tmpDir);
        }
    }

    /**
     * @param string $fixtureDir
     * @param string $expectedUrl
     * @dataProvider configLoadDataProvider
     */
    public function testConfigLoad($fixtureDir, $expectedUrl)
    {
        $bootstrap = new \Magento\TestFramework\Performance\Bootstrap(
            $this->appBootstrap,
            $fixtureDir,
            $this->_getBaseFixtureDir() . '/app_base_dir'
        );
        $config = $bootstrap->getConfig();
        $this->assertInstanceOf('Magento\TestFramework\Performance\Config', $config);
        $this->assertEquals($expectedUrl, $config->getApplicationUrlHost());
    }

    /**
     * @return array
     */
    public function configLoadDataProvider()
    {
        $baseFixtureDir = $this->_getBaseFixtureDir();
        return array(
            'config.php.dist' => array('fixtureDir' => $baseFixtureDir . '/config_dist', 'expectedUrl' => '127.0.0.1'),
            'config.dist' => array('fixtureDir' => $baseFixtureDir . '/config_normal', 'expectedUrl' => '192.168.0.1')
        );
    }

    /**
     * Return path to directory, utilized for bootstrap
     *
     * @return string
     */
    protected function _getBaseFixtureDir()
    {
        return __DIR__ . '/_files/bootstrap';
    }

    public function testCleanupReportsCreatesDirectory()
    {
        $fixtureDir = $this->_getBaseFixtureDir() . '/config_dist';
        $bootstrap = new \Magento\TestFramework\Performance\Bootstrap($this->appBootstrap, $fixtureDir, $fixtureDir);

        $reportDir = $fixtureDir . '/tmp/subdirectory/report';

        $this->assertFalse(is_dir($reportDir));
        $bootstrap->cleanupReports();
        $this->assertTrue(is_dir($reportDir));
    }

    public function testCleanupReportsRemovesFiles()
    {
        $fixtureDir = $this->_getBaseFixtureDir() . '/config_dist';
        $bootstrap = new \Magento\TestFramework\Performance\Bootstrap($this->appBootstrap, $fixtureDir, $fixtureDir);

        $reportDir = $fixtureDir . '/tmp/subdirectory/report';
        mkdir($reportDir, 0777, true);
        $reportFile = $reportDir . '/a.jtl';
        touch($reportFile);

        $this->assertFileExists($reportFile);
        $bootstrap->cleanupReports();
        $this->assertFileNotExists($reportFile);
    }
}
