<?php
class Memory_Usage
{
    const LOG_DIR = 'log';

    /**
     * @var Memory_Usage
     */
    protected static $_instance = null;

    /**
     * @var resource
     */
    protected $_log = null;
    protected $_logFile = null;

    protected static $_lastMem = 0;
    protected static $_fullMem = 0;

    protected static $_logMin = 1048576;

    protected function __construct($logFile)
    {
        $this->_logFile = $logFile;
    }

    protected static function _getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self(self::LOG_DIR . '/' . time() . '.log');
            if (!is_dir(self::LOG_DIR)) {
                mkdir(self::LOG_DIR, 0777, true);
            }
            if (file_exists(self::$_instance->_logFile)) {
                unlink(self::$_instance->_logFile);
            }
            self::$_instance->_log = fopen(self::$_instance->_logFile, 'a');
            fputs(self::$_instance->_log,
                "| current memory | system mem | system mem delta | system mem full delta |  message\n"
            );
        }
        return self::$_instance;
    }

    public function __destructor()
    {
        if (null !== self::$_instance->_log) {
            fclose(self::$_instance->_log);
        }
    }

    public static function log($message, $test = '')
    {
        $_instance = self::_getInstance();
        $currentMemory = memory_get_peak_usage(true);
        $sysMem = self::_getSystemMemory();
        $sysDelta = $sysMem - self::$_lastMem;
        self::$_fullMem += $sysDelta;
        self::$_lastMem = $sysMem;

        if (!empty($test)) {
            $test = ', test: ' . $test;
        }
        fputs($_instance->_log,
            sprintf("| %s | %s | %s | %s | %s%s\n%s\n",
                str_pad(self::_getHumanReadableMemory($currentMemory), 14, ' ', STR_PAD_BOTH),
                str_pad(self::_getHumanReadableMemory($sysMem), 10, ' ', STR_PAD_BOTH),
                str_pad(self::_getHumanReadableMemory($sysDelta), 16, ' ', STR_PAD_BOTH),
                str_pad(self::_getHumanReadableMemory(self::$_fullMem), 21, ' ', STR_PAD_BOTH),
                $message,
                $test,
                str_repeat('-', 100)
            ));
        return $sysMem;
    }

    protected static function _getHumanReadableMemory($value)
    {
        $units = array('K', 'M');
        $unit = 'B';
        while (abs($value) > 1024) {
            $value = round($value / 1024);
            $unit = array_shift($units);
            if (count($units) == 0) {
                break;
            }
        }
        return $value . ' ' . $unit;
    }

    protected static function _getSystemMemory()
    {
        /* for Windows */
        $memoryStr =  exec('tasklist /FI "PID eq '. getmypid() .'"', $data, $returnVar);
        unset($data, $returnVar);
        if (preg_match("/([\d,]+) (K|M)$/", $memoryStr, $match)) {
            $memory = str_replace(',', '', $match[1]);
        } else {
            $memory = 0;
        }
        $multiplier = 1024;
        if ($match[2] == 'M') {
            $multiplier *= 1024;
        }
        return $memory * $multiplier;
    }
}
