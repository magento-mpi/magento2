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
 * Scenario for performance tests
 */
class Magento_Scenario
{
    /**
     * Common scenario arguments
     */
    const ARGUMENT_HOST  = 'host';
    const ARGUMENT_PATH  = 'path';
    const ARGUMENT_LOOPS = 'loops';
    const ARGUMENT_USERS = 'users';
    const ARGUMENT_ADMIN_USERNAME = 'admin_username';
    const ARGUMENT_ADMIN_PASSWORD = 'admin_password';
    const ARGUMENT_ADMIN_FRONTNAME = 'admin_frontname';

    /**
     * @var Magento_Shell
     */
    protected $_shell;

    /**
     * @var string
     */
    protected $_jMeterJarFile;
    /**
     * @var string
     */
    protected $_reportDir;

    /**
     * Constructor
     *
     * @param Magento_Shell $shell
     * @param string $jMeterJarFile
     * @param string $reportDir
     * @throws Magento_Exception
     */
    public function __construct(Magento_Shell $shell, $jMeterJarFile, $reportDir)
    {
        $this->_shell = $shell;
        $this->_jMeterJarFile = $jMeterJarFile;
        $this->_reportDir = $reportDir;

        $this->_validateScenarioExecutable();
        $this->_ensureReportDirExists();
    }

    /**
     * Validate whether scenario executable is available in the environment
     */
    protected function _validateScenarioExecutable()
    {
        $this->_shell->execute('java -jar %s --version', array($this->_jMeterJarFile));
    }

    /**
     * Create writable reports directory, if it does not exist
     */
    protected function _ensureReportDirExists()
    {
        if (!is_dir($this->_reportDir)) {
            mkdir($this->_reportDir, 0777, true);
        }
    }

    /**
     * Run performance testing scenario and write results to the report file
     *
     * @param string $scenarioFile
     * @param array $scenarioConfig
     * @throws Magento_Exception
     */
    public function run($scenarioFile, array $scenarioConfig)
    {
        if (!file_exists($scenarioFile)) {
            throw new Magento_Exception("Scenario file '$scenarioFile' does not exist.");
        }
        $scenarioArgs = isset($scenarioConfig['arguments']) ? $scenarioConfig['arguments'] : array();

        if (empty($scenarioArgs[self::ARGUMENT_HOST]) || empty($scenarioArgs[self::ARGUMENT_PATH])) {
            throw new Magento_Exception(sprintf(
                "Scenario arguments '%s' and '%s' must be specified.", self::ARGUMENT_HOST, self::ARGUMENT_PATH
            ));
        }

        // Dry run - just to warm-up the system
        if (empty($scenarioConfig['settings']['skip_dry_run'])) {
            $dryScenarioArgs = array_merge($scenarioArgs, array(self::ARGUMENT_USERS => 1, self::ARGUMENT_LOOPS => 2));
            $this->_runScenario($scenarioFile, $dryScenarioArgs);
        }

        // Full run
        $fullScenarioArgs = $scenarioArgs + array(self::ARGUMENT_USERS => 1, self::ARGUMENT_LOOPS => 1);
        $reportFile = $this->_reportDir . DIRECTORY_SEPARATOR . basename($scenarioFile, '.jmx') . '.jtl';
        $this->_runScenario($scenarioFile, $fullScenarioArgs, $reportFile);
    }

    /**
     * Run performance testing scenario.
     *
     * @param string $scenarioFile
     * @param array $scenarioArgs
     * @param string|null $reportFile
     */
    protected function _runScenario($scenarioFile, array $scenarioArgs, $reportFile = null)
    {
        list($scenarioCmd, $scenarioCmdArgs) = $this->_buildScenarioCmd($scenarioFile, $scenarioArgs, $reportFile);
        $this->_shell->execute($scenarioCmd, $scenarioCmdArgs);
        if ($reportFile) {
            $this->_verifyReport($reportFile);
        }
    }

    /**
     * Build and return scenario execution command and arguments for it
     *
     * @param string $scenarioFile
     * @param array $scenarioArgs
     * @param string|null $reportFile
     * @return array
     */
    protected function _buildScenarioCmd($scenarioFile, array $scenarioArgs, $reportFile = null)
    {
        $command = 'java -jar %s -n -t %s';
        $arguments = array($this->_jMeterJarFile, $scenarioFile);
        if ($reportFile) {
            $command .= ' -l %s';
            $arguments[] = $reportFile;
        }
        foreach ($scenarioArgs as $key => $value) {
            $command .= ' %s';
            $arguments[] = "-J$key=$value";
        }
        return array($command, $arguments);
    }

    /**
     * Verify that report XML structure contains no failures and no errors
     *
     * @param string $reportFile
     * @throws Magento_Exception
     */
    protected function _verifyReport($reportFile)
    {
        if (!file_exists($reportFile)) {
            throw new Magento_Exception("Report file '$reportFile' has not been created.");
        }
        $reportXml = simplexml_load_file($reportFile);

        $failedAssertions = $reportXml->xpath('//assertionResult[failure[text()="true"] or error[text()="true"]]');
        if ($failedAssertions) {
            $failureMessages = array("Scenario has failed.");
            foreach ($failedAssertions as $assertionResult) {
                if (isset($assertionResult->failureMessage)) {
                    $failureMessages[] = (string)$assertionResult->failureMessage;
                }
                if (isset($assertionResult->errorMessage)) {
                    $failureMessages[] = (string)$assertionResult->errorMessage;
                }
            }
            throw new Magento_Exception(implode(PHP_EOL, $failureMessages));
        }
    }
}
