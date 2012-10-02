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
class Magento_Bootstrap
{
    /**
     * Base directory for performance tests
     *
     * @var string
     */
    protected $_baseDir;

    /**
     * Tests configuration holder
     *
     * @var Magento_Config
     */
    protected $_config;

    /**
     * Installer for the application
     *
     * @var Magento_Installer
     */
    protected $_installer;

    /**
     * @param string $baseDir
     */
    public function __construct($baseDir)
    {
        $this->_baseDir = $baseDir;

        $this->_loadConfig()
            ->_cleanReports();
    }

    /**
     * Loads configuration for the tests
     *
     * @return Magento_Bootstrap
     */
    protected function _loadConfig()
    {
        $configFile = "{$this->_baseDir}/config.php";
        $configFile = file_exists($configFile) ? $configFile : "$configFile.dist";
        $configData = require($configFile);

        $this->_config = new Magento_Config($configData, $this->_baseDir);

        return $this;
    }

    /**
     * Clean report directory
     *
     * @return Magento_Bootstrap
     * @throws Magento_Exception
     */
    protected function _cleanReports()
    {
        $reportDir = $this->_config->getReportDir();
        if (file_exists($reportDir) && !Varien_Io_File::rmdirRecursive($reportDir)) {
            throw new Magento_Exception("Cannot cleanup reports directory '$reportDir'.");
        }
        return $this;
    }

    /**
     * Return configuration for the tests
     *
     * @return Magento_Config
     */
    public function getConfig()
    {
        return $this->_config;
    }
}
