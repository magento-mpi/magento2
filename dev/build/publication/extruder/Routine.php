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
            echo implode("\n", $output) . "\n";
        }

        if (!$ignore && $exitCode > 0) {
            return $exitCode;
        }

        return 0;
    }

    /**
     *  Parse path if enumeration found
     *
     * @static
     * @param $path
     * @return array
     */
    public static function parsePath($path)
    {
        $matches = $result = array();
        if (preg_match('/{([^}]+)}/U', $path, $matches) > 0) {
            foreach (explode(',', $matches[1]) as $match) {
                $newPath = preg_replace('/{' . $matches[1] . '}/U', trim($match), $path);
                $result = array_merge($result, self::parsePath($newPath));
            }
        } else {
            $result[] = $path;
        }

        return array_unique($result);
    }
}
