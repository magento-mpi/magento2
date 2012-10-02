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
class Magento_Config
{
    /**
     * Default value for configuration of benchmarking executable file path
     */
    const DEFAULT_JMETER_JAR_FILE = 'ApacheJMeter.jar';

    /**
     * @var Zend_Log
     */
    protected $_logger = null;

    /**
     * @var string
     */
    protected $_applicationBaseDir;

    /**
     * @var string
     */
    protected $_applicationUrlHost;

    /**
     * @var string
     */
    protected $_applicationUrlPath;

    /**
     * @var array
     */
    protected $_adminOptions = array();

    /**
     * @var string
     */
    protected $_reportDir;

    /**
     * @var string
     */
    protected $_jMeterPath;

    /**
     * @var array
     */
    protected $_installOptions = array();

    /**
     * @var array
     */
    protected $_scenarios = array();

    /**
     * Constructor
     *
     * @param array $configData
     * @param string $baseDir
     * @param Zend_Log|null
     * @throws InvalidArgumentException
     * @throws Magento_Exception
     */
    public function __construct(array $configData, $baseDir, $logger = null)
    {
        $this->_logger = $logger;

        $this->_validateData($configData);
        if (!is_dir($baseDir)) {
            throw new Magento_Exception("Base directory '$baseDir' does not exist.");
        }
        $this->_reportDir = $baseDir . DIRECTORY_SEPARATOR . $configData['report_dir'];

        $applicationOptions = $configData['application'];
        $this->_applicationBaseDir = realpath($baseDir . '/../../../');
        $this->_applicationUrlHost = $applicationOptions['url_host'];
        $this->_applicationUrlPath = $applicationOptions['url_path'];
        $this->_adminOptions = $applicationOptions['admin'];

        if (isset($applicationOptions['installation'])) {
            $installConfig = $applicationOptions['installation'];
            $this->_installOptions = $installConfig['options'];
        }

        if (!empty($configData['scenario']['jmeter_jar_file'])) {
            $this->_jMeterPath = $configData['scenario']['jmeter_jar_file'];
        } else {
            $this->_jMeterPath = getenv('jmeter_jar_file') ?: self::DEFAULT_JMETER_JAR_FILE;
        }

        $this->_expandScenarios($configData['scenario'], $baseDir);
    }

    /**
     * Expands scenario options and file paths glob to a list of scenarios
     * @param array $scenarios
     * @param string $baseDir
     * @throws InvalidArgumentException
     * @throws Magento_Exception
     */
    protected function _expandScenarios($scenarios, $baseDir)
    {
        if (!isset($scenarios['scenarios'])) {
            return;
        }
        if (!is_array($scenarios['scenarios'])) {
            throw new InvalidArgumentException("'scenario' => 'scenarios' option must be an array");
        }

        $commonScenarioConfig = $this->_composeCommonScenarioConfig($scenarios);
        foreach ($scenarios['scenarios'] as $scenarioName => $scenarioConfig) {
            // Scenarios without additional settings can be presented as direct values of 'scenario' array
            if (!is_array($scenarioConfig)) {
                $scenarioName = $scenarioConfig;
                $scenarioConfig = array();
            }

            // Scenario file
            $scenarioFile = realpath($baseDir . DIRECTORY_SEPARATOR . $scenarioName);
            if (!file_exists($scenarioFile)) {
                throw new Magento_Exception("Scenario '$scenarioName' doesn't exist in $baseDir");
            }

            // Compose config, using global config
            $scenarioConfig = $this->_overwriteByArray($commonScenarioConfig, $scenarioConfig);

            // Fixtures
            $scenarioConfig['fixtures'] = $this->_expandScenarioFixtures($scenarioConfig, $baseDir);

            // Store scenario
            $this->_scenarios[$scenarioFile] = $scenarioConfig;
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
        // Validate 1st-level options data
        $requiredKeys = array('application', 'scenario', 'report_dir');
        foreach ($requiredKeys as $requiredKeyName) {
            if (empty($configData[$requiredKeyName])) {
                throw new Magento_Exception("Configuration array must define '$requiredKeyName' key.");
            }
        }

        // Validate admin options data
        $requiredAdminKeys = array('frontname', 'username', 'password');
        foreach ($requiredAdminKeys as $requiredKeyName) {
            if (empty($configData['application']['admin'][$requiredKeyName])) {
                throw new Magento_Exception("Admin options array must define '$requiredKeyName' key.");
            }
        }
    }

    /**
     * Compose list of all parameters, that must be provided for all scenarios
     *
     * @param array $scenarios
     * @return array
     */
    protected function _composeCommonScenarioConfig($scenarios)
    {
        $adminOptions = $this->getAdminOptions();
        $result = array(
            'arguments' => array(
                Magento_Scenario::ARG_HOST => $this->getApplicationUrlHost(),
                Magento_Scenario::ARG_PATH => $this->getApplicationUrlPath(),
                Magento_Scenario::ARG_ADMIN_FRONTNAME => $adminOptions['frontname'],
                Magento_Scenario::ARG_ADMIN_USERNAME => $adminOptions['username'],
                Magento_Scenario::ARG_ADMIN_PASSWORD => $adminOptions['password'],
            ),
            'settings' => array(),
            'fixtures' => array()
        );

        if (isset($scenarios['common_config'])) {
            $result = $this->_overwriteByArray($result, $scenarios['common_config']);
        }

        return $result;
    }

    /**
     * Merge array values from $source into $target's array values
     *
     * @param array $target
     * @param array $source
     * @return array
     */
    protected function _overwriteByArray(array $target, array $source)
    {
        foreach ($source as $key => $sourceVal) {
            if (!empty($target[$key])) {
                $target[$key] = array_merge($target[$key], $sourceVal);
            } else {
                $target[$key] = $sourceVal;
            }
        }
        return $target;
    }

    /**
     * Process fixture file names from scenario config and compose array of full file paths to them
     *
     * @param array $scenarioConfig
     * @param string $baseDir
     * @return array
     * @throws InvalidArgumentException|Magento_Exception
     */
    protected function _expandScenarioFixtures(array $scenarioConfig, $baseDir)
    {
        if (!is_array($scenarioConfig['fixtures'])) {
            throw new InvalidArgumentException(
                "Scenario 'fixtures' option must be an array, not a value: '{$scenarioConfig['fixtures']}'"
            );
        }

        $result = array();
        foreach ($scenarioConfig['fixtures'] as $fixtureName) {
            $fixtureFile = $baseDir . DIRECTORY_SEPARATOR . $fixtureName;
            if (!file_exists($fixtureFile)) {
                throw new Magento_Exception("Fixture '$fixtureName' doesn't exist in $baseDir");
            }
            $result[] = $fixtureFile;
        }

        return $result;
    }

    /**
     * Retrieve application base directory
     *
     * @return string
     */
    public function getApplicationBaseDir()
    {
        return $this->_applicationBaseDir;
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
     * Retrieve admin options - backend path and admin user credentials
     *
     * @return array
     */
    public function getAdminOptions()
    {
        return $this->_adminOptions;
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
     * Retrieve scenario files and their configuration as specified in the config
     *
     * @return array
     */
    public function getScenarios()
    {
        return $this->_scenarios;
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

    /**
     * Retrieves path to JMeter java file
     *
     * @return string
     */
    public function getJMeterPath()
    {
        return $this->_jMeterPath;
    }

    /**
     * Get logger, which is used to output messages
     *
     * @return Zend_Log
     */
    public function getLogger()
    {
        if (!$this->_logger) {
            $writer = new Zend_Log_Writer_Stream('php://output');
            $formatter = new Zend_Log_Formatter_Simple('%message%' . PHP_EOL);
            $writer->setFormatter($formatter);
            $this->_logger = new Zend_Log($writer);
        }
        return $this->_logger;
    }
}
