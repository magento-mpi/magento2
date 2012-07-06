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
class Benchmark_Scenario
{
    /**
     * Common scenario parameters
     */
    const PARAM_HOST  = 'host';
    const PARAM_PATH  = 'path';
    const PARAM_LOOPS = 'loops';
    const PARAM_USERS = 'users';

    /**
     * @var string
     */
    protected $_scenarioFile;

    /**
     * @var array
     */
    protected $_scenarioParams;

    /**
     * @var string
     */
    protected $_reportDir;

    /**
     * @var string
     */
    protected $_reportFile;

    /**
     * @var string
     */
    protected $_jMeterJarFile;

    /**
     * @var Magento_Shell
     */
    protected $_shell;

    /**
     * Constructor
     *
     * @param string $scenarioFile
     * @param array $scenarioParams
     * @param string $reportDir
     * @param string $jMeterJarFile
     * @param Magento_Shell $shell
     * @throws Magento_Exception
     */
    public function __construct($scenarioFile, array $scenarioParams, $reportDir, $jMeterJarFile, Magento_Shell $shell)
    {
        if (!file_exists($scenarioFile)) {
            throw new Magento_Exception("Scenario file '$scenarioFile' does not exist.");
        }
        if (empty($scenarioParams[self::PARAM_HOST]) || empty($scenarioParams[self::PARAM_PATH])) {
            throw new Magento_Exception(sprintf(
                "Scenario parameters '%s' and '%s' must be specified.", self::PARAM_HOST, self::PARAM_PATH
            ));
        }
        $this->_scenarioFile = $scenarioFile;
        $this->_scenarioParams = $scenarioParams + array(self::PARAM_USERS => 1, self::PARAM_LOOPS => 1);
        $this->_reportDir = $reportDir;
        $this->_reportFile = $this->_reportDir . DIRECTORY_SEPARATOR . basename($this->_scenarioFile, '.jmx') . '.jtl';
        $this->_jMeterJarFile = $jMeterJarFile;
        $this->_shell = $shell;
    }

    /**
     * Run performance testing scenario and write results to report file
     */
    public function run()
    {
        $this->_validateScenarioExecutable();
        $this->_ensureReportDirExists();
        list($scenarioCmd, $scenarioCmdArgs) = $this->_buildScenarioCmd($this->_scenarioParams, $this->_reportFile);
        $this->_shell->execute($scenarioCmd, $scenarioCmdArgs);
        $this->_verifyReport($this->_loadReportXml());
    }

    /**
     * Run performance testing scenario without writing and result report
     *
     * @param int $loops
     */
    public function runDry($loops = 1)
    {
        $this->_validateScenarioExecutable();
        $scenarioParams = array(self::PARAM_USERS => 1, self::PARAM_LOOPS => $loops);
        $scenarioParams = array_merge($this->_scenarioParams, $scenarioParams);
        list($scenarioCmd, $scenarioCmdArgs) = $this->_buildScenarioCmd($scenarioParams);
        $this->_shell->execute($scenarioCmd, $scenarioCmdArgs);
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
     * Build and return scenario execution command and arguments for it
     *
     * @param array $scenarioParams
     * @param string|null $reportFile
     * @return array
     */
    protected function _buildScenarioCmd(array $scenarioParams, $reportFile = null)
    {
        $command = 'java -jar %s -n -t %s';
        $arguments = array($this->_jMeterJarFile, $this->_scenarioFile);
        if ($reportFile) {
            $command .= ' -l %s';
            $arguments[] = $reportFile;
        }
        foreach ($scenarioParams as $key => $value) {
            $command .= ' %s';
            $arguments[] = "-J$key=$value";
        }
        return array($command, $arguments);
    }

    /**
     * Load results from the XML report file
     *
     * @return SimpleXMLElement
     * @throws Magento_Exception
     */
    protected function _loadReportXml()
    {
        if (!file_exists($this->_reportFile)) {
            throw new Magento_Exception("Report file '$this->_reportFile' has not been created.");
        }
        return simplexml_load_file($this->_reportFile);
    }

    /**
     * Verify that report XML structure contains no failures and errors
     *
     * @param SimpleXMLElement $reportXml
     * @throws Magento_Exception
     */
    protected function _verifyReport(SimpleXMLElement $reportXml)
    {
        $failedAssertions = $reportXml->xpath('//assertionResult[failure[text()="true"] or error[text()="true"]]');
        if ($failedAssertions) {
            $failureMessages = array("Scenario '$this->_scenarioFile' has failed.");
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
