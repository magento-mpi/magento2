<?php
/**
 * Storage for timers statistics
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Profiler_Driver_Standard_Stat
{
    /**
     * #@+ Timer statistics data keys
     */
    const NAME = 'name';
    const START = 'start';
    const TIME = 'sum';
    const COUNT = 'count';
    const AVG = 'avg';
    const REALMEM = 'realmem';
    const REALMEM_START = 'realmem_start';
    const EMALLOC = 'emalloc';
    const EMALLOC_START = 'emalloc_start';
    /**#@-*/

    /**
     * Array of timers statistics data
     *
     * @var array
     */
    protected $_timers = array();

    /**
     * Starts timer
     *
     * @param string $timerName
     * @param int $time
     * @param int $realMemory Real size of memory allocated from system
     * @param int $emallocMemory Memory used by emalloc()
     */
    public function start($timerName, $time, $realMemory, $emallocMemory)
    {
        if (empty($this->_timers[$timerName])) {
            $this->_timers[$timerName] = array(
                self::START   => false,
                self::TIME    => 0,
                self::COUNT   => 0,
                self::REALMEM => 0,
                self::EMALLOC => 0,
            );
        }

        $this->_timers[$timerName][self::REALMEM_START] = $realMemory;
        $this->_timers[$timerName][self::EMALLOC_START] = $emallocMemory;
        $this->_timers[$timerName][self::START] = $time;
        $this->_timers[$timerName][self::COUNT]++;
    }

    /**
     * Stops timer
     *
     * @param string $timerName
     * @param int $time
     * @param int $realMemory Real size of memory allocated from system
     * @param int $emallocMemory Memory used by emalloc()
     * @throws InvalidArgumentException if timer doesn't exist
     */
    public function stop($timerName, $time, $realMemory, $emallocMemory)
    {
        if (empty($this->_timers[$timerName])) {
            throw new InvalidArgumentException(sprintf('Timer "%s" doesn\'t exist.', $timerName));
        }

        $this->_timers[$timerName][self::TIME] += ($time - $this->_timers[$timerName]['start']);
        $this->_timers[$timerName][self::START] = false;
        $this->_timers[$timerName][self::REALMEM] += $realMemory;
        $this->_timers[$timerName][self::REALMEM] -= $this->_timers[$timerName][self::REALMEM_START];
        $this->_timers[$timerName][self::EMALLOC] += $emallocMemory;
        $this->_timers[$timerName][self::EMALLOC] -= $this->_timers[$timerName][self::EMALLOC_START];
    }

    /**
     * Get timer statistics data by timer name
     *
     * @param string $timerName
     * @return array
     * @throws InvalidArgumentException if timer doesn't exist
     */
    public function get($timerName)
    {
        if (empty($this->_timers[$timerName])) {
            throw new InvalidArgumentException(sprintf('Timer "%s" doesn\'t exist.', $timerName));
        }
        return $this->_timers[$timerName];
    }

    /**
     * Retrieve statistics on specified timer
     *
     * @param $timerName
     * @param string $key Information to return
     * @return int|float
     * @throws InvalidArgumentException
     */
    public function fetch($timerName, $key)
    {
        if ($key === self::NAME) {
            return $timerName;
        }
        if (empty($this->_timers[$timerName])) {
            throw new InvalidArgumentException(sprintf('Timer "%s" doesn\'t exist.', $timerName));
        }
        /* FETCH_AVG = FETCH_TIME / FETCH_COUNT */
        $isAvg = ($key == self::AVG);
        if ($isAvg) {
            $key = self::TIME;
        }
        if (!isset($this->_timers[$timerName][$key])) {
            throw new InvalidArgumentException(sprintf('Timer "%s" doesn\'t have value for "%s".', $timerName, $key));
        }
        $result = $this->_timers[$timerName][$key];
        if ($key == self::TIME && $this->_timers[$timerName][self::START] !== false) {
            $result += (microtime(true) - $this->_timers[$timerName][self::START]);
        }
        if ($isAvg) {
            $count = $this->_timers[$timerName][self::COUNT];
            if ($count) {
                $result = $result / $count;
            }
        }
        return $result;
    }

    /**
     * Reset collected statistics for specified timer or for all timers if timer name is omitted
     *
     * @param string|null $timerName
     */
    public function reset($timerName = null)
    {
        if ($timerName) {
            unset($this->_timers[$timerName]);
        } else {
            $this->_timers = array();
        }
    }

    /**
     * Get ordered list of timer names filtered by thresholds and name pattern
     *
     * @param array|null $thresholds
     * @param string|null $filterPattern
     * @return array
     */
    public function getFilteredTimerNames(array $thresholds = null, $filterPattern = null)
    {
        $timerNames = $this->_getOrderedTimerNames();
        if (!$thresholds && !$filterPattern) {
            return $timerNames;
        }
        $thresholds = (array)$thresholds;
        $result = array();
        foreach ($timerNames as $timerName) {
            /* Filter by pattern */
            if ($filterPattern && !preg_match($filterPattern, $timerName)) {
                continue;
            }
            /* Filter by thresholds */
            $match = true;
            foreach ($thresholds as $fetchKey => $minMatchValue) {
                $match = ($this->fetch($timerName, $fetchKey) >= $minMatchValue);
                if ($match) {
                    break;
                }
            }
            if ($match) {
                $result[] = $timerName;
            }
        }
        return $result;
    }

    /**
     * Get ordered list of timer names
     *
     * @return array
     */
    protected function _getOrderedTimerNames()
    {
        $timerNames = array_keys($this->_timers);
        if (count($timerNames) <= 2) {
            /* No sorting needed */
            return $timerNames;
        }

        /* Prepare PCRE once to use it inside the loop body */
        $nestingSep = preg_quote(Magento_Profiler::NESTING_SEPARATOR, '/');
        $patternLastTimerName = '/' . $nestingSep . '(?:.(?!' . $nestingSep . '))+$/';

        $prevTimerId = $timerNames[0];
        $result = array($prevTimerId);
        for ($i = 1; $i < count($timerNames); $i++) {
            $timerId = $timerNames[$i];
            /* Skip already added timer */
            if (!$timerId) {
                continue;
            }
            /* Loop over all timers that need to be closed under previous timer */
            while (strpos($timerId, $prevTimerId . Magento_Profiler::NESTING_SEPARATOR) !== 0) {
                /* Add to result all timers nested in the previous timer */
                for ($j = $i + 1; $j < count($timerNames); $j++) {
                    if (strpos($timerNames[$j], $prevTimerId . Magento_Profiler::NESTING_SEPARATOR) === 0) {
                        $result[] = $timerNames[$j];
                        /* Mark timer as already added */
                        $timerNames[$j] = null;
                    }
                }
                /* Go to upper level timer */
                $count = 0;
                $prevTimerId = preg_replace($patternLastTimerName, '', $prevTimerId, -1, $count);
                /* Break the loop if no replacements was done. It is possible when we are */
                /* working with top level (root) item */
                if (!$count) {
                    break;
                }
            }
            /* Add current timer to the result */
            $result[] = $timerId;
            $prevTimerId = $timerId;
        }
        return $result;
    }
}
