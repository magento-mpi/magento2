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

    /**
     * List of default tags.
     *
     * @var array
     */
    static private $_defaultTags = array();

    /**
     * Collection of tag filters.
     *
     * @var array
     */
    static private $_tagFilters = array();

    /**
     * Has tag filters flag to faster checks of filters availability.
     *
     * @var bool
     */
    static private $_hasTagFilters = false;

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
     * Add tag filter.
     *
     * @param string $tagName
     * @param string $tagValue
     */
    public static function addTagFilter($tagName, $tagValue)
    {
        if (!isset(self::$_tagFilters[$tagName])) {
            self::$_tagFilters[$tagName] = array();
        }
        self::$_tagFilters[$tagName][] = $tagValue;
        self::$_hasTagFilters = true;
    }

    /**
     * Check tags with tag filters.
     *
     * @param array|null $tags
     * @return bool
     */
    private static function _checkTags(array $tags = null)
    {
        if (self::$_hasTagFilters) {
            if (is_array($tags)) {
                $keysToCheck = array_intersect(array_keys(self::$_tagFilters), array_keys($tags));
                if ($keysToCheck) {
                    foreach ($keysToCheck as $keyToCheck) {
                        if (in_array($tags[$keyToCheck], self::$_tagFilters[$keyToCheck])) {
                            return true;
                        }
                    }
                }
            }
            return false;
        }
        return true;
    }

    /**
     * Add profiler driver.
     *
     * @param Magento_Profiler_DriverInterface $driver
     */
    public static function add(Magento_Profiler_DriverInterface $driver)
    {
        self::$_drivers[get_class($driver)] = $driver;
        self::enable();
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
     * @param array|null $tags
     * @return array|null
     */
    private static function _getTags(array $tags = null)
    {
        if (self::$_defaultTags) {
            return array_merge((array)$tags, self::$_defaultTags);
        } else {
            return $tags;
        }
    }

    /**
     * Enable profiling.
     *
     * Any call to profiler does nothing until profiler is enabled.
     */
    public static function enable()
    {
        self::$_enabled = true;
    }

    /**
     * Disable profiling.
     *
     * Any call to profiler is silently ignored while profiler is disabled.
     */
    public static function disable()
    {
        self::$_enabled = false;
    }

    /**
     * Get profiler enable status.
     *
     * @return bool
     */
    public static function isEnabled()
    {
        return self::$_enabled;
    }

    /**
     * Reset collected statistics for specified timer or for whole profiler if timer name is omitted
     *
     * @param string|null $timerId
     */
    public static function reset($timerId = null)
    {
        if (!self::isEnabled()) {
            return;
        }

        /** @var Magento_Profiler_DriverInterface $driver */
        foreach (self::$_drivers as $driver) {
            $driver->reset($timerId);
        }

        if ($timerId === null) {
            self::$_currentPath = array();
            self::$_tagFilters = array();
            self::$_defaultTags = array();
            self::$_hasTagFilters = false;
            self::$_drivers = array();
            return;
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
        $tags = self::_getTags($tags);
        if (!self::isEnabled() || !self::_checkTags($tags)) {
            return;
        }

        if (strpos($timerName, self::NESTING_SEPARATOR) !== false) {
            throw new Varien_Exception('Timer name must not contain a nesting separator.');
        }

        $timerId = self::_getTimerId($timerName);
        /** @var Magento_Profiler_DriverInterface $driver */
        foreach (self::$_drivers as $driver) {
            $driver->start($timerId, $tags);
        }
        /* Continue collecting timers statistics under the latest started one */
        self::$_currentPath[] = $timerName;
    }

    /**
     * Stop recording statistics for specified timer.
     *
     * Call with no arguments to stop the recently started timer.
     * Only the latest started timer can be stopped.
     *
     * @param string|null $timerName
     * @throws Varien_Exception
     */
    public static function stop($timerName = null)
    {
        if (!self::$_enabled || !self::_checkTags(self::_getTags())) {
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

        $timerId = self::_getTimerId();
        /** @var Magento_Profiler_DriverInterface $driver */
        foreach (self::$_drivers as $driver) {
            $driver->stop($timerId);
        }
    }
}
