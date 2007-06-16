<?php

class Varien_Profiler
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
        #return true;
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
    
    public static function reset($timerName)
    {
        self::$_timers[$timerName] = array('start'=>0, 'count'=>0, 'sum'=>0);
    }
    
    public static function resume($timerName)
    {
        if (empty(self::$_timers[$timerName])) {
            self::reset($timerName);
        }
        self::$_timers[$timerName]['start'] = microtime(true);
        self::$_timers[$timerName]['count'] ++;
    }
    
    public static function start($timerName)
    {
        self::resume($timerName);
    }
    
    public static function pause($timerName)
    {
        if (empty(self::$_timers[$timerName])) {
            self::reset($timerName);
        }
        self::$_timers[$timerName]['sum'] += microtime(true)-self::$_timers[$timerName]['start'];
        self::$_timers[$timerName]['start'] = false;
    }
    
    public static function stop($timerName)
    {
        self::pause($timerName);
    }
    
    public static function fetch($timerName, $key='sum')
    {
        if (empty(self::$_timers[$timerName])) {
            return false;
        } elseif (empty($key)) {
            return self::$_timers[$timerName];
        }
        switch ($key) {
            case 'sum':
                $sum = self::$_timers[$timerName]['sum'];
                if (self::$_timers[$timerName]['start']!==false) {
                    $sum += microtime(true)-self::$_timers[$timerName]['start'];
                }
                return $sum;
                
            case 'count':
                $count = self::$_timers[$timerName]['count'];
                if (self::$_timers[$timerName]['start']!==false) {
                    $count ++;
                }
                return $count;
                
            default:
                if (!empty(self::$_timers[$timerName][$key])) {
                    return self::$_timers[$timerName][$key];
                }
        }
        return false;
    }
    
    public static function getTimerSum($timerName=false)
    {
        if (false!==$timerName) {
            return self::fetch($timerName, 'sum');
        }
        $timers = array();
        foreach (self::$_timers as $timerName=>$timer) {
            $timers[$timerName] = self::fetch($timerName);
        }
        return $timers;
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
    
    public static function stopAllCumulativeTimers()
    {
        foreach (self::$_cumulativeTimers as $timerName=>$timer) {
            self::setTimer($timerName, true);
        }
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
    public static function getSqlProfiler($res) {
        if(!$res){
            return false;
        }
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