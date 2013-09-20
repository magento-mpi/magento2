<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     performance_tests
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Performance;

class BootstrapTest extends \PHPUnit_Framework_TestCase
{

    public function tearDown()
    {
        // Delete a directory, where tests do some temporary work
        $tmpDir = $this->_getBaseFixtureDir() . '/config_dist/tmp';
        \Magento\Io\File::rmdirRecursive($tmpDir);
    }

    /**
     * @param string $fixtureDir
     * @param string $expectedUrl
     * @dataProvider configLoadDataProvider
     */
    public function testConfigLoad($fixtureDir, $expectedUrl)
    {
        $bootstrap =
            new \Magento\TestFramework\Performance\Bootstrap($fixtureDir, $this->_getBaseFixtureDir() . '/app_base_dir');
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
            'config.php.dist' => array(
                'fixtureDir' => $baseFixtureDir . '/config_dist',
                'expectedUrl' => '127.0.0.1'
            ),
            'config.dist' => array(
                'fixtureDir' => $baseFixtureDir . '/config_normal',
                'expectedUrl' => '192.168.0.1'
            ),
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
        $bootstrap = new \Magento\TestFramework\Performance\Bootstrap($fixtureDir, $fixtureDir);

        $reportDir = $fixtureDir . '/tmp/subdirectory/report';

        $this->assertFalse(is_dir($reportDir));
        $bootstrap->cleanupReports();
        $this->assertTrue(is_dir($reportDir));
    }

    public function testCleanupReportsRemovesFiles()
    {
        $fixtureDir = $this->_getBaseFixtureDir() . '/config_dist';
        $bootstrap = new \Magento\TestFramework\Performance\Bootstrap($fixtureDir, $fixtureDir);

        $reportDir = $fixtureDir . '/tmp/subdirectory/report';
        mkdir($reportDir, 0777, true);
        $reportFile = $reportDir . '/a.jtl';
        touch($reportFile);

        $this->assertFileExists($reportFile);
        $bootstrap->cleanupReports();
        $this->assertFileNotExists($reportFile);
    }
}
