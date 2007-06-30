<?php

class Varien_Profiler
{

    /**
     * Timers for code profiling
     *
     * @var array
     */
    static private $_timers = array();
    static private $_enabled = false;
    
    public static function enable()
    {
        self::$_enabled = true;
    }
    
    public static function disable()
    {
        self::$_enabled = false;
    }
    
    public static function reset($timerName)
    {
        self::$_timers[$timerName] = array('start'=>false, 'count'=>0, 'sum'=>0);
    }
    
    public static function resume($timerName)
    {
        if (!self::$_enabled) {
            return;
        }
        
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
        if (!self::$_enabled) {
            return;
        }
        
        if (empty(self::$_timers[$timerName])) {
            self::reset($timerName);
        }
        if (false!==self::$_timers[$timerName]['start']) {
            self::$_timers[$timerName]['sum'] += microtime(true)-self::$_timers[$timerName]['start'];
            self::$_timers[$timerName]['start'] = false;
        }
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
                return $count;
                
            default:
                if (!empty(self::$_timers[$timerName][$key])) {
                    return self::$_timers[$timerName][$key];
                }
        }
        return false;
    }
    
    public static function getTimers()
    {
        return self::$_timers;
    }

    /**
     * Output SQl Zend_Db_Profiler
     *
     */
    public static function getSqlProfiler($res) {
        if(!$res){
            return '';
        }
        $out = '';
        $profiler = $res->getProfiler();
        if($profiler->getEnabled()) {
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

            $out .= 'Executed ' . $queryCount . ' queries in ' . $totalTime . ' seconds' . "<br>";
            $out .= 'Average query length: ' . $totalTime / $queryCount . ' seconds' . "<br>";
            $out .= 'Queries per second: ' . $queryCount / $totalTime . "<br>";
            $out .= 'Longest query length: ' . $longestTime . "<br>";
            $out .= 'Longest query: <br>' . $longestQuery . "<hr>";
        }
        return $out;
    }
}