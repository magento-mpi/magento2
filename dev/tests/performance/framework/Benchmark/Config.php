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
 * Configuration of performance tests
 */
class Benchmark_Config
{
    /**
     * @var string
     */
    protected $_applicationUrlHost;

    /**
     * @var string
     */
    protected $_applicationUrlPath;

    /**
     * @var string
     */
    protected $_reportDir;

    /**
     * @var array
     */
    protected $_installOptions = array();

    /**
     * @var array
     */
    protected $_fixtureFiles = array();

    /**
     * @var array
     */
    protected $_scenarios = array();

    /**
     * Constructor
     *
     * @param array $configData
     * @param string $baseDir
     * @throws Magento_Exception
     */
    public function __construct(array $configData, $baseDir)
    {
        $this->_validateData($configData);
        if (!is_dir($baseDir)) {
            throw new Magento_Exception("Base directory '$baseDir' does not exist.");
        }
        $baseDir = str_replace('\\', '/', realpath($baseDir));
        $this->_reportDir = $baseDir . '/' . $configData['report_dir'];
        $this->_applicationUrlHost = $configData['application']['url_host'];
        $this->_applicationUrlPath = $configData['application']['url_path'];

        if (isset($configData['application']['installation'])) {
            $installConfig = $configData['application']['installation'];
            $this->_installOptions = $installConfig['options'];
            if (isset($installConfig['fixture_files'])) {
                $this->_fixtureFiles = glob($baseDir . '/' . $installConfig['fixture_files'], GLOB_BRACE);
            }
        }

        if (isset($configData['scenario']['common_params'])) {
            $scenarioParamsCommon = $configData['scenario']['common_params'];
        } else {
            $scenarioParamsCommon = array();
        }

        $scenarioFilesPattern = $baseDir . '/' . $configData['scenario']['files'];
        $scenarioFiles = glob($scenarioFilesPattern, GLOB_BRACE);
        if (!$scenarioFiles) {
            throw new Magento_Exception("No scenario files match '$scenarioFilesPattern' pattern.");
        }
        foreach ($scenarioFiles as $oneScenarioFile) {
            $oneScenarioFile = str_replace('\\', '/', realpath($oneScenarioFile));
            $oneScenarioName = substr($oneScenarioFile, strlen($baseDir) + 1);
            if ($baseDir . '/' . $oneScenarioName != $oneScenarioFile) {
                throw new Magento_Exception("Scenario file '$oneScenarioFile' must reside in '$baseDir' directory.");
            }
            if (isset($configData['scenario']['scenario_params'][$oneScenarioName])) {
                $oneScenarioParams = $configData['scenario']['scenario_params'][$oneScenarioName];
            } else {
                $oneScenarioParams = array();
            }
            $this->_scenarios[$oneScenarioFile] = array_merge($scenarioParamsCommon, $oneScenarioParams);
        }
    }

    /**
     * Validate high-level configuration structure
     *
     * @param array $configData
     * @throws Magento_Exception
     */
    protected function _validateData(array $configData)
    {
        $requiredKeys = array('application', 'scenario', 'report_dir');
        foreach ($requiredKeys as $requiredKeyName) {
            if (empty($configData[$requiredKeyName])) {
                throw new Magento_Exception("Configuration array must define '$requiredKeyName' key.");
            }
        }
    }

    /**
     * Retrieve application URL host component
     *
     * @return string
     */
    public function getApplicationUrlHost()
    {
        return $this->_applicationUrlHost;
    }

    /**
     * Retrieve application URL path component
     *
     * @return string
     */
    public function getApplicationUrlPath()
    {
        return $this->_applicationUrlPath;
    }

    /**
     * Retrieve application installation options
     *
     * @return array
     */
    public function getInstallOptions()
    {
        return $this->_installOptions;
    }

    /**
     * Retrieve scenario files and their parameters as array('<scenario_file>' => '<scenario_params>', ...)
     *
     * @return array
     */
    public function getScenarios()
    {
        return $this->_scenarios;
    }

    /**
     * Retrieve fixture script files
     *
     * @return array
     */
    public function getFixtureFiles()
    {
        return $this->_fixtureFiles;
    }

    /**
     * Retrieve reports directory
     *
     * @return string
     */
    public function getReportDir()
    {
        return $this->_reportDir;
    }
}
