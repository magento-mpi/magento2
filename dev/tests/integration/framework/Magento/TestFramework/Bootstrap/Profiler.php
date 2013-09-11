<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Bootstrap of the application profiler
 */
class Magento_TestFramework_Bootstrap_Profiler
{
    /**
     * Profiler driver instance
     *
     * @var Magento_Profiler_Driver_Standard
     */
    protected $_driver;

    /**
     * Whether a profiler driver has been already registered or not
     *
     * @var bool
     */
    protected $_isDriverRegistered = false;

    /**
     * Constructor
     *
     * @param Magento_Profiler_Driver_Standard $driver
     */
    public function __construct(Magento_Profiler_Driver_Standard $driver)
    {
        $this->_driver = $driver;
    }

    /**
     * Register profiler driver to involve it into the results processing
     */
    protected function _registerDriver()
    {
        if (!$this->_isDriverRegistered) {
            $this->_isDriverRegistered = true;
            Magento_Profiler::add($this->_driver);
        }
    }

    /**
     * Register file-based profiling
     *
     * @param string $profilerOutputFile
     */
    public function registerFileProfiler($profilerOutputFile)
    {
        $this->_registerDriver();
        $this->_driver->registerOutput(new Magento_Profiler_Driver_Standard_Output_Csvfile(array(
            'filePath' => $profilerOutputFile
        )));
    }

    /**
     * Register profiler with Bamboo-friendly output format
     *
     * @param string $profilerOutputFile
     * @param string $profilerMetricsFile
     */
    public function registerBambooProfiler($profilerOutputFile, $profilerMetricsFile)
    {
        $this->_registerDriver();
        $this->_driver->registerOutput(new Magento_TestFramework_Profiler_OutputBamboo(array(
            'filePath' => $profilerOutputFile,
            'metrics'  => require($profilerMetricsFile)
        )));
    }
}
