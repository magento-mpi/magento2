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
 * Handler for performance testing scenarios in format of PHP console scripts
 */
class Magento_Performance_Scenario_Handler_Php implements Magento_Performance_Scenario_HandlerInterface
{
    /**
     * @var Magento_Shell
     */
    protected $_shell;

    /**
     * @var bool
     */
    protected $_validateExecutable;

    /**
     * Constructor
     *
     * @param Magento_Shell $shell
     * @param bool $validateExecutable
     */
    public function __construct(Magento_Shell $shell, $validateExecutable = true)
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
            $this->_shell->execute('php --version');
        }
    }

    /**
     * Run scenario and optionally write results to report file
     *
     * @param string $scenarioFile
     * @param Magento_Performance_Scenario_Arguments $scenarioArguments
     * @param string|null $reportFile Report file to write results to, NULL disables report creation
     * @throws Magento_Performance_Scenario_FailureException
     *
     * @todo Implement execution in concurrent threads defined by the "users" scenario argument
     */
    public function run($scenarioFile, Magento_Performance_Scenario_Arguments $scenarioArguments, $reportFile = null)
    {
        $this->_validateScenarioExecutable();
        $reportRows = array();
        for ($i = 0; $i < $scenarioArguments->getLoops(); $i++) {
            $oneReportRow = $this->_executeScenario($scenarioFile, $scenarioArguments);
            $reportRows[] = $oneReportRow;
        }
        if ($reportFile) {
            $this->_writeReport($reportRows, $reportFile);
        }
        $reportErrors = $this->_getReportErrors($reportRows);
        if ($reportErrors) {
            throw new Magento_Performance_Scenario_FailureException(
                $scenarioFile, $scenarioArguments, implode(PHP_EOL, $reportErrors)
            );
        }
    }

    /**
     * Execute scenario file and return measurement results
     *
     * @param string $scenarioFile
     * @param Traversable $scenarioArgs
     * @return array
     */
    protected function _executeScenario($scenarioFile, Traversable $scenarioArgs)
    {
        list($scenarioCmd, $scenarioCmdArgs) = $this->_buildScenarioCmd($scenarioFile, $scenarioArgs);
        $result = array(
            'scenario'  => $scenarioFile,
            'timestamp' => time(),
            'success'   => true,
            'time'      => null,
            'exit_code' => 0,
            'output'    => '',
        );
        $executionTime = microtime(true);
        try {
            $result['output'] = $this->_shell->execute($scenarioCmd, $scenarioCmdArgs);
        } catch (Magento_Exception $e) {
            $result['success']   = false;
            $result['exit_code'] = $e->getPrevious()->getCode();
            $result['output']    = $e->getPrevious()->getMessage();
        }
        $executionTime = (microtime(true) - $executionTime);
        $executionTime *= 1000; // second -> millisecond
        $result['time'] = (int)round($executionTime);
        return $result;
    }

    /**
     * Build and return scenario execution command and arguments for it, compatible with the getopt() "long options"
     * @link http://www.php.net/getopt
     *
     * @param string $scenarioFile
     * @param Traversable $scenarioArgs
     * @return array
     */
    protected function _buildScenarioCmd($scenarioFile, Traversable $scenarioArgs)
    {
        $command = 'php -f %s --';
        $arguments = array($scenarioFile);
        foreach ($scenarioArgs as $paramName => $paramValue) {
            $command .= " --$paramName %s";
            $arguments[] = $paramValue;
        }
        return array($command, $arguments);
    }

    /**
     * Write report into file in Apache JMeter's JTL format
     * @link http://wiki.apache.org/jmeter/JtlTestLog
     *
     * @param array $reportRows
     * @param string $reportFile
     */
    protected function _writeReport(array $reportRows, $reportFile)
    {
        $xml = array();
        $xml[] = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml[] = '<testResults version="1.2">';
        foreach ($reportRows as $index => $oneReportRow) {
            $xml[] = '<httpSample'
                . ' t="' . $oneReportRow['time'] . '"'
                . ' lt="0"'
                . ' ts="' . $oneReportRow['timestamp'] . '"'
                . ' s="' . ($oneReportRow['success'] ? 'true' : 'false') . '"'
                . ' lb="' . $oneReportRow['scenario'] . '"'
                . ' rc="' . $oneReportRow['exit_code'] . '"'
                . ' rm=""'
                . ' tn="Sample ' . ($index + 1) . '"'
                . ' dt="text"'
                . '/>';
        }
        $xml[] = '</testResults>';
        file_put_contents($reportFile, implode(PHP_EOL, $xml));
    }

    /**
     * Retrieve error messages from the report
     *
     * @param array $reportRows
     * @return array
     */
    protected function _getReportErrors(array $reportRows)
    {
        $result = array();
        foreach ($reportRows as $oneReportRow) {
            if (!$oneReportRow['success']) {
                $result[] = $oneReportRow['output'];
            }
        }
        return $result;
    }
}
