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
 * Handler for performance testing scenarios in format of Apache JMeter
 */
class Magento_Performance_Scenario_Handler_Jmeter implements Magento_Performance_Scenario_HandlerInterface
{
    /**
     * @var Magento_Shell
     */
    protected $_shell;

    /**
     * Constructor
     *
     * @param Magento_Shell $shell
     */
    public function __construct(Magento_Shell $shell)
    {
        $this->_shell = $shell;
        $this->_validateScenarioExecutable();
    }

    /**
     * Validate whether scenario executable is available in the environment
     */
    protected function _validateScenarioExecutable()
    {
        $this->_shell->execute('jmeter --version');
    }

    /**
     * Run scenario and optionally write results to report file
     *
     * @param string $scenarioFile
     * @param Magento_Performance_Scenario_Arguments $scenarioArguments
     * @param string|null $reportFile Report file to write results to, NULL disables report creation
     * @return bool Whether handler was able to process scenario
     */
    public function run($scenarioFile, Magento_Performance_Scenario_Arguments $scenarioArguments, $reportFile = null)
    {
        if (pathinfo($scenarioFile, PATHINFO_EXTENSION) != 'jmx') {
            return false;
        }
        list($scenarioCmd, $scenarioCmdArgs) = $this->_buildScenarioCmd($scenarioFile, $scenarioArguments, $reportFile);
        $this->_shell->execute($scenarioCmd, $scenarioCmdArgs);
        if ($reportFile) {
            $this->_verifyReport($reportFile);
        }
        return true;
    }

    /**
     * Build and return scenario execution command and arguments for it
     *
     * @param string $scenarioFile
     * @param Traversable $scenarioArgs
     * @param string|null $reportFile
     * @return array
     */
    protected function _buildScenarioCmd($scenarioFile, Traversable $scenarioArgs, $reportFile = null)
    {
        $command = 'jmeter -n -t %s';
        $arguments = array($scenarioFile);
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
     * @link http://wiki.apache.org/jmeter/JtlTestLog
     *
     * @param string $reportFile
     * @throws Magento_Exception
     * @throws Magento_Performance_Scenario_FailureException
     */
    protected function _verifyReport($reportFile)
    {
        if (!file_exists($reportFile)) {
            throw new Magento_Exception("Report file '$reportFile' has not been created.");
        }
        $reportXml = simplexml_load_file($reportFile);
        $failedAssertions = $reportXml->xpath(
            '/testResults/*/assertionResult[failure[text()="true"] or error[text()="true"]]'
        );
        if ($failedAssertions) {
            $failureMessages = array();
            foreach ($failedAssertions as $assertionResult) {
                if (isset($assertionResult->failureMessage)) {
                    $failureMessages[] = (string)$assertionResult->failureMessage;
                }
                if (isset($assertionResult->errorMessage)) {
                    $failureMessages[] = (string)$assertionResult->errorMessage;
                }
            }
            throw new Magento_Performance_Scenario_FailureException(implode(PHP_EOL, $failureMessages));
        }
    }
}
