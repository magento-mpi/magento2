<?php
/**
 * Interface for profiler driver.
 *
 * Implementation of this interface is responsible for logic of profiling.
 *
 * @copyright {}
 */
interface Magento_Profiler_DriverInterface
{
    /**
     * Start timer
     *
     * @param string $timerId
     * @param array|null $tags
     */
    public function start($timerId, array $tags = null);

    /**
     * Stop timer
     *
     * @param string $timerId
     */
    public function stop($timerId);

    /**
     * Reset collected statistics for specified timer or for whole profiler if timer name is omitted.
     *
     * @param string|null $timerId
     */
    public function reset($timerId = null);
}
