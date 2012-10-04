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

class Magento_Performance_BootstrapTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $fixtureDir
     * @param string $expectedUrl
     * @dataProvider configLoadDataProvider
     */
    public function testConfigLoad($fixtureDir, $expectedUrl)
    {
        $bootstrap = new Magento_Performance_Bootstrap($fixtureDir, $this->_getBaseFixtureDir() . '/app_base_dir');
        $config = $bootstrap->getConfig();
        $this->assertInstanceOf('Magento_Performance_Config', $config);
        $this->assertEquals($expectedUrl, $config->getApplicationUrlHost());
    }

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

    public function testCleanupReports()
    {
        $fixtureDir = $this->_getBaseFixtureDir() . '/config_dist';
        $reportDir = $fixtureDir . '/report';
        mkdir($reportDir, 0777);

        try {
            $reportFile = $reportDir . '/a.jtl';
            touch($reportFile);
            $this->assertFileExists($reportFile);

            $bootstrap = new Magento_Performance_Bootstrap($fixtureDir, $fixtureDir);
            $bootstrap->cleanupReports();
            $this->assertFileNotExists($reportFile);
            $this->assertFileExists($reportDir);

            Varien_Io_File::rmdirRecursive($reportDir);
        } catch (Exception $e) {
            Varien_Io_File::rmdirRecursive($reportDir);
            throw $e;
        }
    }
}
