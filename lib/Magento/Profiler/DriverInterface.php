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
     * @param string $timerName
     * @param array|null $tags
     */
    public function start($timerName, array $tags = null);

    /**
     * Stop timer
     *
     * @param string $timerName
     * @param array|null $tags
     */
    public function stop($timerName = null, array $tags = null);

    /**
     * Enable profiling.
     */
    public function enable();

    /**
     * Disabled profiling.
     */
    public function disable();
}
