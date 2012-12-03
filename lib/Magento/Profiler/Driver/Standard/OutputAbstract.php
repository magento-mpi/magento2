<?php
/**
 * Abstract class that represents profiler standard driver output
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
abstract class Magento_Profiler_Driver_Standard_OutputAbstract
    implements Magento_Profiler_Driver_Standard_OutputInterface
{
    /**
     * PCRE Regular Expression for filter timer by name
     *
     * @var null|string
     */
    protected $_filterPattern;

    /**
     * List of threshold (minimal allowed) values for profiler data
     *
     * @var array
     */
    protected $_thresholds = array(
        Magento_Profiler_Driver_Standard_Stat::TIME => 0.001,
        Magento_Profiler_Driver_Standard_Stat::COUNT => 10,
        Magento_Profiler_Driver_Standard_Stat::EMALLOC => 10000,
    );

    /**
     * Set profiler output with timer identifiers filter.
     *
     * @param string $filterPattern PCRE pattern to filter timers by their identifiers
     */
    public function setFilterPattern($filterPattern)
    {
        $this->_filterPattern = $filterPattern;
    }

    /**
     * Set threshold (minimal allowed) value for timer column.
     *
     * Timer is being rendered if at least one of its columns is not less than the minimal allowed value.
     *
     * @param string $fetchKey
     * @param int|float|null $minAllowedValue
     */
    public function setThreshold($fetchKey, $minAllowedValue)
    {
        if ($minAllowedValue === null) {
            unset($this->_thresholds[$fetchKey]);
        } else {
            $this->_thresholds[$fetchKey] = $minAllowedValue;
        }
    }

    /**
     * Retrieve the list of (column_label; column_id) pairs
     *
     * @return array
     */
    protected function _getColumns()
    {
        return array(
            'Timer Id' => Magento_Profiler_Driver_Standard_Stat::NAME,
            'Time'     => Magento_Profiler_Driver_Standard_Stat::TIME,
            'Avg'      => Magento_Profiler_Driver_Standard_Stat::AVG,
            'Cnt'      => Magento_Profiler_Driver_Standard_Stat::COUNT,
            'Emalloc'  => Magento_Profiler_Driver_Standard_Stat::EMALLOC,
            'RealMem'  => Magento_Profiler_Driver_Standard_Stat::REALMEM,
        );
    }

    /**
     * Render statistics column value for specified timer
     *
     * @param mixed $value
     * @param string $columnKey
     * @return string
     */
    protected function _renderColumnValue($value, $columnKey)
    {
        switch ($columnKey) {
            case Magento_Profiler_Driver_Standard_Stat::NAME:
                $result = $this->_renderTimerName($value);
                break;
            case Magento_Profiler_Driver_Standard_Stat::TIME:
            case Magento_Profiler_Driver_Standard_Stat::AVG:
                $result = number_format($value, 6);
                break;
            default:
                $result = number_format((string)$value);
        }
        return $result;
    }

    /**
     * Render timer name
     *
     * @param string $timerName
     * @return string
     */
    protected function _renderTimerName($timerName)
    {
        return $timerName;
    }

    /**
     * Retrieve the list of timer names from timer statistics object.
     *
     * Timer names will be ordered and filtered by thresholds and name filter pattern.
     *
     * @param Magento_Profiler_Driver_Standard_Stat $stat
     * @return array
     */
    protected function _getTimerNames(Magento_Profiler_Driver_Standard_Stat $stat)
    {
        return $stat->getFilteredTimerNames($this->_thresholds, $this->_filterPattern);
    }

    /**
     * Render a caption for the profiling results
     *
     * @return string
     */
    protected function _renderCaption()
    {
        return sprintf(
            'Code Profiler (Memory usage: real - %s, emalloc - %s)',
            memory_get_usage(true),
            memory_get_usage()
        );
    }
}
