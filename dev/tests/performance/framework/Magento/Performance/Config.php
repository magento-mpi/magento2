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
class Magento_Performance_Config
{
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
     * @param string $testsBaseDir
     * @param string $appBaseDir
     * @throws InvalidArgumentException
     * @throws Magento_Exception
     */
    public function __construct(array $configData, $testsBaseDir, $appBaseDir)
    {
        $this->_validateData($configData);
        if (!is_dir($testsBaseDir)) {
            throw new Magento_Exception("Base directory '$testsBaseDir' does not exist.");
        }
        $this->_reportDir = $testsBaseDir . DIRECTORY_SEPARATOR . $configData['report_dir'];

        $applicationOptions = $configData['application'];
        $this->_applicationBaseDir = $appBaseDir;
        $this->_applicationUrlHost = $applicationOptions['url_host'];
        $this->_applicationUrlPath = $applicationOptions['url_path'];
        $this->_adminOptions = $applicationOptions['admin'];

        if (isset($applicationOptions['installation']['options'])) {
            $this->_installOptions = $applicationOptions['installation']['options'];
        }

        $this->_expandScenarios($configData['scenario'], $testsBaseDir);
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
     * Expands scenario file paths, options and settings with the values, common to all scenarios
     *
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

        // Compose additional configuration for scenarios
        $commonConfig = isset($scenarios['common_config']) ? $scenarios['common_config'] : array();
        $fixedArguments = $this->_getScenarioFixedArguments();

        // Parse scenario config data and add scenarios to config
        foreach ($scenarios['scenarios'] as $scenarioTitle => $scenarioConfigData) {
            $scenario = new Magento_Performance_Config_Scenario($scenarioTitle, $scenarioConfigData, $commonConfig,
                $fixedArguments, $baseDir);
            $this->_scenarios[] = $scenario;
        }
    }

    /**
     * Compose list of default parameters for all scenarios, based on common scenario config and internal values
     *
     * @return array
     */
    protected function _getScenarioFixedArguments()
    {
        $adminOptions = $this->getAdminOptions();
        return array(
            Magento_Performance_Config_Scenario::ARG_HOST            => $this->getApplicationUrlHost(),
            Magento_Performance_Config_Scenario::ARG_PATH            => $this->getApplicationUrlPath(),
            Magento_Performance_Config_Scenario::ARG_BASEDIR         => $this->getApplicationBaseDir(),
            Magento_Performance_Config_Scenario::ARG_ADMIN_FRONTNAME => $adminOptions['frontname'],
            Magento_Performance_Config_Scenario::ARG_ADMIN_USERNAME  => $adminOptions['username'],
            Magento_Performance_Config_Scenario::ARG_ADMIN_PASSWORD  => $adminOptions['password'],
        );
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
     * Retrieve scenario configurations - array of Magento_Performance_Config_Scenario
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
}
