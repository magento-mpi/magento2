<?php
/**
 * Service routines for license-tool command line script
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   build
 * @package    extruder
 * @copyright  Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
