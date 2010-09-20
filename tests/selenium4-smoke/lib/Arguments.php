<?php
/**
 * Command line arguments container
 *
 * @author Magento Inc.
 */
class Arguments {
    /**
     * Command line options
     *
     * @var array
     */
    public static $options = array();

    /**
     * Array of the variables passed via command line as
     * --set <VAR_NAME>=<VALUE>
     *
     * @var array
     */
    public static $variables = array();

    /**
     * Parse command line options
     */
    public static function init()
    {
        $args = $_SERVER['argv'];
        array_shift($args);
        
        $varFlag = false;

        foreach ($args as $arg) {
            if ($arg == '--set' || $arg == '-s') {
                $varFlag = true;
            } elseif ($varFlag) {
                list($key, $val) = explode('=', $arg);
                self::$variables[$key] = $val;
                $varFlag = false;
            } else {
                self::$options[] = $arg;
            }
        }
    }
}
