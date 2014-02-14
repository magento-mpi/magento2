<?php
/**
 * Interface for profiler driver.
 *
 * Implementation of this interface is responsible for logic of profiling.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Profiler;

interface DriverInterface
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
     * Clear collected statistics for specified timer or for whole profiler if timer name is omitted.
     *
     * @param string|null $timerId
     */
    public function clear($timerId = null);
}
