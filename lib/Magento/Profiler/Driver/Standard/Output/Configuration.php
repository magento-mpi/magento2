<?php
/**
 * Profiler driver standard output configuration.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Profiler_Driver_Standard_Output_Configuration extends Magento_Profiler_Driver_Configuration
{
    /**
     * @#+
     * Specific configuration option names
     */
    const FILTER_PATTERN_OPTION = 'filterPattern';
    const THRESHOLDS_OPTION = 'thresholds';
    /**@#-*/

    /**
     * Get "filter pattern" option value
     *
     * @param mixed $default
     * @return string|null
     */
    public function getFilterPatternValue($default = null)
    {
        return $this->getStringValue(self::FILTER_PATTERN_OPTION, $default);
    }

    /**
     * Set "filter pattern" option value
     *
     * @param string $filterPattern
     */
    public function setFilterPatternValue($filterPattern)
    {
        $this->setValue(self::FILTER_PATTERN_OPTION, $filterPattern);
    }

    /**
     * Is "filter pattern" option has a value
     *
     * @return bool
     */
    public function hasFilterPatternValue()
    {
        return $this->hasValue(self::FILTER_PATTERN_OPTION);
    }

    /**
     * Get "thresholds" option value
     *
     * @param array $default
     * @return array
     */
    public function getThresholdsValue(array $default = array())
    {
        return $this->getArrayValue(self::THRESHOLDS_OPTION, $default);
    }

    /**
     * Set "thresholds" option value
     *
     * @param string $thresholds
     */
    public function setThresholdsValue(array $thresholds)
    {
        $this->setValue(self::THRESHOLDS_OPTION, $thresholds);
    }

    /**
     * Is "thresholds" option has a value
     *
     * @return bool
     */
    public function hasThresholdsValue()
    {
        return $this->hasValue(self::THRESHOLDS_OPTION);
    }
}
