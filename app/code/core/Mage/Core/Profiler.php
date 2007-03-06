<?php

class Mage_Core_Profiler
{

    /**
     * Timers for code profiling
     *
     * @var array
     */
    static private $_timers;
    
    /**
     * Cumulative timers
     * 
     * @var array
     */
    static private $_cumulativeTimers;
    
    /**
     * Set timer to current microtime and return delta from previous timer value
     *
     * @param string $timerName
     * @return float
     */
    public static function setTimer($timerName, $cumulative=false)
    {
        if (!$cumulative) {
            $oldTimer = isset(self::$_timers[$timerName]) ? self::$_timers[$timerName] : false;
            self::$_timers[$timerName] = microtime(true);
            return self::$_timers[$timerName]-$oldTimer;
        } else {
            if (!isset(self::$_cumulativeTimers[$timerName])) {
               self::$_cumulativeTimers[$timerName] = array(0, 0);
           }
           self::$_cumulativeTimers[$timerName][0] += self::getTimer($timerName);
           self::$_cumulativeTimers[$timerName][1] ++;
        }
    }

    /**
     * Get delta from previous timer value and print if requested
     *
     * @param string $timerName
     * @param boolean $print
     * @return float
     */
    public static function getTimer($timerName, $print=false)
    {
        if (!isset(self::$_timers[$timerName])) {
            return false;
        }
        $delta = microtime(true)-self::$_timers[$timerName];
        if ($print) {
            echo "<hr>$timerName execution time: $delta<hr>";
        }
        return $delta;
    }
    
    public static function getCumulativeTimer($timerName='')
    {
        if (''===$timerName) {
            return self::$_cumulativeTimers;
        }
        
        if (!isset(self::$_cumulativeTimers[$timerName])) {
            return false;
        }
        return self::$_cumulativeTimers[$timerName];
    }

    /**
     * Output SQl Zend_Db_Profiler
     *
     */
    public static function getSqlProfiler() {
        $res = Mage_Core_Resource::getResource('dev_write')->getConnection();
        $profiler = $res->getProfiler();
        if($profiler->getEnabled())
        {
            $totalTime    = $profiler->getTotalElapsedSecs();
            $queryCount   = $profiler->getTotalNumQueries();
            $longestTime  = 0;
            $longestQuery = null;

            foreach ($profiler->getQueryProfiles() as $query) {
                if ($query->getElapsedSecs() > $longestTime) {
                    $longestTime  = $query->getElapsedSecs();
                    $longestQuery = $query->getQuery();
                }
            }

            echo 'Executed ' . $queryCount . ' queries in ' . $totalTime . ' seconds' . "<br>";
            echo 'Average query length: ' . $totalTime / $queryCount . ' seconds' . "<br>";
            echo 'Queries per second: ' . $queryCount / $totalTime . "<br>";
            echo 'Longest query length: ' . $longestTime . "<br>";
            echo 'Longest query: <br>' . $longestQuery . "<hr>";
        
            echo '<pre>cumulative: '.print_r(self::getCumulativeTimer(),1).'</pre>';
        }
    }
}