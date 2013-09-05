<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     performance_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Bootstrap for performance tests
 */
class Magento_TestFramework_Performance_Bootstrap
{
    /**
     * Tests configuration holder
     *
     * @var Magento_TestFramework_Performance_Config
     */
    protected $_config;

    /**
     * Constructor
     *
     * @param string $testsBaseDir
     * @param string $appBaseDir
     */
    public function __construct($testsBaseDir, $appBaseDir)
    {
        $configFile = "$testsBaseDir/config.php";
        $configFile = file_exists($configFile) ? $configFile : "$configFile.dist";
        $configData = require $configFile;
        $this->_config = new Magento_TestFramework_Performance_Config($configData, $testsBaseDir, $appBaseDir);
    }

    /**
     * Ensure reports directory exists, empty, and has write permissions
     *
     * @throws \Magento\Exception
     */
    public function cleanupReports()
    {
        $reportDir = $this->_config->getReportDir();
        if (file_exists($reportDir) && !\Magento\Io\File::rmdirRecursive($reportDir)) {
            throw new \Magento\Exception("Cannot cleanup reports directory '$reportDir'.");
        }
        mkdir($reportDir, 0777, true);
    }

    /**
     * Return configuration for the tests
     *
     * @return Magento_TestFramework_Performance_Config
     */
    public function getConfig()
    {
        return $this->_config;
    }
}
