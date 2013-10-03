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
namespace Magento\TestFramework\Performance\Scenario\Handler;

class Jmeter
    implements \Magento\TestFramework\Performance\Scenario\HandlerInterface
{
    /**
     * @var \Magento\Shell
     */
    protected $_shell;

    /**
     * @var bool
     */
    protected $_validateExecutable;

    /**
     * Constructor
     *
     * @param \Magento\Shell $shell
     * @param bool $validateExecutable
     */
    public function __construct(\Magento\Shell $shell, $validateExecutable = true)
    {
        $this->_shell = $shell;
        $this->_validateExecutable = $validateExecutable;
    }

    /**
     * Validate whether scenario executable is available in the environment
     */
    protected function _validateScenarioExecutable()
    {
        if ($this->_validateExecutable) {
            $this->_validateExecutable = false; // validate only once
            $this->_shell->execute('jmeter --version');
        }
    }

    /**
     * Run scenario and optionally write results to report file
     *
     * @param \Magento\TestFramework\Performance\Scenario $scenario
     * @param string|null $reportFile Report file to write results to, NULL disables report creation
     * @throws \Magento\Exception
     * @throws \Magento\TestFramework\Performance\Scenario\FailureException
     */
    public function run(\Magento\TestFramework\Performance\Scenario $scenario, $reportFile = null)
    {
        $this->_validateScenarioExecutable();

        $cmd = $this->_buildScenarioCmd($scenario, $reportFile);
        list($scenarioCmd, $scenarioCmdArgs) = $cmd;
        $this->_shell->execute($scenarioCmd, $scenarioCmdArgs);

        if ($reportFile) {
            if (!file_exists($reportFile)) {
                throw new \Magento\Exception(
                    "Report file '$reportFile' for '{$scenario->getTitle()}' has not been created."
                );
            }
            $reportErrors = $this->_getReportErrors($reportFile);
            if ($reportErrors) {
                throw new \Magento\TestFramework\Performance\Scenario\FailureException($scenario,
                    implode(PHP_EOL, $reportErrors));
            }
        }
    }

    /**
     * Build and return scenario execution command and arguments for it
     *
     * @param \Magento\TestFramework\Performance\Scenario $scenario
     * @param string|null $reportFile
     * @return array
     */
    protected function _buildScenarioCmd(\Magento\TestFramework\Performance\Scenario $scenario, $reportFile = null)
    {
        $command = 'jmeter -n -t %s';
        $arguments = array($scenario->getFile());
        if ($reportFile) {
            $command .= ' -l %s';
            $arguments[] = $reportFile;
        }
        foreach ($scenario->getArguments() as $key => $value) {
            $command .= ' %s';
            $arguments[] = "-J$key=$value";
        }
        return array($command, $arguments);
    }

    /**
     * Retrieve error/failure messages from the report file
     * @link http://wiki.apache.org/jmeter/JtlTestLog
     *
     * @param string $reportFile
     * @return array
     */
    protected function _getReportErrors($reportFile)
    {
        $result = array();
        $reportXml = simplexml_load_file($reportFile);
        $failedAssertions = $reportXml->xpath(
            '/testResults/*/assertionResult[failure[text()="true"] or error[text()="true"]]'
        );
        if ($failedAssertions) {
            foreach ($failedAssertions as $assertionResult) {
                if (isset($assertionResult->failureMessage)) {
                    $result[] = (string)$assertionResult->failureMessage;
                }
                if (isset($assertionResult->errorMessage)) {
                    $result[] = (string)$assertionResult->errorMessage;
                }
            }
        }
        return $result;
    }
}
