<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Tools\View\Deployer;

/**
 * An echo-logger with separating types of messages
 */
class Log
{
    /**#@+
     * Bitmasks for verbosity level
     */
    const SILENT = 0;
    const ERROR = 1;
    const DEBUG = 2;
    /**#@-*/

    /**
     * @var int
     */
    private $verbosity;

    /**
     * @param int $verbosity
     */
    public function __construct($verbosity)
    {
        $this->verbosity = (int)$verbosity;
    }

    /**
     * Log anything
     *
     * @param string $msg
     */
    public function log($msg)
    {
        if ($this->verbosity !== self::SILENT) {
            echo "{$msg}\n";
        }
    }

    /**
     * Log an error
     *
     * @param string $msg
     */
    public function logError($msg)
    {
        if ($this->verbosity & self::ERROR) {
            echo "ERROR: {$msg}\n";
        }
    }

    /**
     * Log a debug message
     *
     * @param string $msg
     */
    public function logDebug($msg)
    {
        if ($this->verbosity & self::DEBUG) {
            echo "{$msg}\n";
        }
    }
} 
