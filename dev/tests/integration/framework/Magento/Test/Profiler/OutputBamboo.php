<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Test
 * @subpackage  integration_test
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class that used for output Magento Profiler results in format compatible with Bamboo Jmeter plugin
 */
class Magento_Test_Profiler_OutputBamboo extends Magento_Profiler_Driver_Standard_Output_Csvfile
{
    /**
     * @var array
     */
    protected $_metrics;

    /**
     * Constructor
     *
     * @param string $filename  Filename of the target file to write results to
     * @param array  $metrics   Metrics to be included into result.
     *                          Supported format: array(
     *                              'metric name 1' => array(
     *                                  'profiler key 1', ...
     *                              ), ...
     *                          );
     * @param string $delimiter
     * @param string $enclosure
     */
    public function __construct($filename, array $metrics, $delimiter = ',', $enclosure = '"')
    {
        parent::__construct($filename, $delimiter, $enclosure);
        $this->_metrics = $metrics;
    }

    /**
     * Calculate metric value from set of timer names
     *
     * @param Magento_Profiler_Driver_Standard_Stat $stat
     * @param array $timerNames
     * @param string $fetchKey
     * @return int
     */
    protected function _aggregateTimerValues(
        Magento_Profiler_Driver_Standard_Stat $stat,
        array $timerNames,
        $fetchKey = Magento_Profiler_Driver_Standard_Stat::AVG
    ) {
        /* Prepare pattern that matches timers with deepest nesting level only */
        $nestingSep = preg_quote(Magento_Profiler::NESTING_SEPARATOR, '/');
        array_map('preg_quote', $timerNames, array('/'));
        $pattern = '/(?<=' . $nestingSep . '|^)(?:' . implode('|', $timerNames) . ')$/';

        /* Sum profiler values for matched timers */
        $result = 0;
        foreach ($this->_getTimerIds($stat) as $timerId) {
            if (preg_match($pattern, $timerId)) {
                $result += $stat->fetch($timerId, $fetchKey);
            }
        }

        /* Convert seconds -> milliseconds */
        $result = round($result * 1000);

        return $result;
    }

    /**
     * Write content into an opened file handle
     *
     * @param resource $fileHandle
     * @param Magento_Profiler_Driver_Standard_Stat $stat
     */
    protected function _writeFileContent($fileHandle, Magento_Profiler_Driver_Standard_Stat $stat)
    {
        /* First column must be a timestamp */
        $result = array('Timestamp' => time());
        foreach ($this->_metrics as $metricName => $timerNames) {
            $result[$metricName] = $this->_aggregateTimerValues($stat, $timerNames);
        }
        fputcsv($fileHandle, array_keys($result), $this->_delimiter, $this->_enclosure);
        fputcsv($fileHandle, array_values($result), $this->_delimiter, $this->_enclosure);
    }
}
