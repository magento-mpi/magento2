<?php
/**
 * Static class that represents profiling tool
 *
 * @copyright {}
 */
class Magento_Profiler
{
    /**
     * Separator literal to assemble timer identifier from timer names
     */
    const NESTING_SEPARATOR = '->';

    /**
     * Whether profiling is active or not
     *
     * @var bool
     */
    static private $_enabled = false;

    /**
     * Nesting path that represents namespace to resolve timer names
     *
     * @var array
     */
    static private $_currentPath = array();

    /**
     * Collection for profiler drivers.
     *
     * @var array
     */
    static private $_drivers = array();

    static private $_defaultTags = array();

    /**
     * Set default tags
     *
     * @param array $tags
     */
    public static function setDefaultTags(array $tags)
    {
        self::$_defaultTags = $tags;
    }

    /**
     * Add profiler driver.
     *
     * @param Magento_Profiler_DriverInterface $driver
     */
    public static function add(Magento_Profiler_DriverInterface $driver)
    {
        self::$_drivers[get_class($driver)] = $driver;
    }

    /**
     * Retrieve unique identifier among all timers
     *
     * @param string|null $timerName Timer name
     * @return string
     */
    private static function _getTimerId($timerName = null)
    {
        $currentPath = self::$_currentPath;
        if ($timerName) {
            $currentPath[] = $timerName;
        }
        return implode(self::NESTING_SEPARATOR, $currentPath);
    }

    /**
     * Get tags list.
     *
     * @param array $tags
     * @return array|null
     */
    private static function _getTags(array $tags = null)
    {
        if (is_array($tags)) {
            $tags = array_merge($tags, self::$_defaultTags);
        }
        return $tags;
    }

    /**
     * Enable profiling.
     *
     * Any call to profiler does nothing until profiler is enabled.
     */
    public static function enable()
    {
        self::$_enabled = true;

        /** @var Magento_Profiler_DriverInterface $driver */
        foreach (self::$_drivers as $driver) {
            $driver->enable();
        }
    }

    /**
     * Disable profiling.
     *
     * Any call to profiler is silently ignored while profiler is disabled.
     */
    public static function disable()
    {
        self::$_enabled = false;

        /** @var Magento_Profiler_DriverInterface $driver */
        foreach (self::$_drivers as $driver) {
            $driver->disable();
        }
    }

    /**
     * Reset collected statistics for specified timer or for whole profiler if timer name is omitted
     *
     * @param string|null $timerId
     */
    public static function reset($timerId = null)
    {
        if ($timerId === null) {
            self::$_currentPath = array();
            return;
        }

        /** @var Magento_Profiler_DriverInterface $driver */
        foreach (self::$_drivers as $driver) {
            $driver->reset($timerId);
        }
    }

    /**
     * Start collecting statistics for specified timer
     *
     * @param string $timerName
     * @param array $tags
     * @throws Varien_Exception
     */
    public static function start($timerName, array $tags = null)
    {
        if (!self::$_enabled) {
            return;
        }

        if (strpos($timerName, self::NESTING_SEPARATOR) !== false) {
            throw new Varien_Exception('Timer name must not contain a nesting separator.');
        }

        /* Continue collecting timers statistics under the latest started one */
        self::$_currentPath[] = $timerName;

        $timerId = self::_getTimerId($timerName);
        /** @var Magento_Profiler_DriverInterface $driver */
        foreach (self::$_drivers as $driver) {
            $driver->start($timerId, self::_getTags($tags));
        }
    }

    /**
     * Stop recording statistics for specified timer.
     *
     * Call with no arguments to stop the recently started timer.
     * Only the latest started timer can be stopped.
     *
     * @param string|null $timerName
     * @param array $tags
     * @throws Varien_Exception
     */
    public static function stop($timerName = null, array $tags = null)
    {
        if (!self::$_enabled) {
            return;
        }

        $latestTimerName = end(self::$_currentPath);
        if ($timerName !== null && $timerName !== $latestTimerName) {
            if (in_array($timerName, self::$_currentPath)) {
                $exceptionMsg = sprintf('Timer "%s" should be stopped before "%s".', $latestTimerName, $timerName);
            } else {
                $exceptionMsg = sprintf('Timer "%s" has not been started.', $timerName);
            }
            throw new Varien_Exception($exceptionMsg);
        }

        /* Move one level up in timers nesting tree */
        array_pop(self::$_currentPath);

        $timerId = self::_getTimerId($timerName);
        /** @var Magento_Profiler_DriverInterface $driver */
        foreach (self::$_drivers as $driver) {
            $driver->stop($timerId, self::_getTags($tags));
        }
    }
}
