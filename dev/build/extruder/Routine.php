<?php
/**
 * {license_notice}
 *
 * @category   build
 * @package    extruder
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Routine run time functions
 *
 */
class Routine
{

    /**
     * Functions executes command and basing on passed parameters shows output, analyzes exit code
     *
     * If ignore set to false returns command exit code in case it differs from 0.
     * If ignore set to true or command executed successfully return 0.
     *
     * @static
     * @param string $cmd
     * @param bool $verbose
     * @param bool $ignore
     * @return int
     */
    public static function execCmd($cmd, $verbose, $ignore)
    {
        $output = array();
        $exitCode = 0;
        exec($cmd, $output, $exitCode);

        if ($verbose) {
            echo $cmd . "\n";
            echo implode("\n", $output);
        }

        if (!$ignore && $exitCode > 0) {
            return $exitCode;
        }

        return 0;
    }
}
