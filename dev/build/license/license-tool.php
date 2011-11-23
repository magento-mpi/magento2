#!/usr/bin/php
<?php
/**
 * Command line tool for processing file docblock of Magento source code files.
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
 * @package    license
 * @copyright  Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require dirname(__FILE__) . '/Routine.php';
require dirname(__FILE__) . '/LicenseAbstract.php';

define('USAGE', <<<USAGE
$>./license-tool.php -c ce.php
    -w dir  use specified working dir instead of current
    -v      verbose output
    -d      dry run

USAGE
);

$shortOpts = 'c:w:vd';
$options = getopt($shortOpts);

if (!isset($options['c'])) {
    print USAGE;
    exit(1);
}

include $options['c'];

$workingDir = '.';
if (isset($options['w'])) {
    $workingDir = rtrim($options['w'], DIRECTORY_SEPARATOR);
}
if (!is_dir($workingDir)) {
    Routine::printLog('Working dir "' . $workingDir . '" does not exist');
    exit(1);
}

if (isset($options['v'])) {
    Routine::$isVerbose = true;
}

$dryRun = false;
if (isset($options['d'])) {
    Routine::$dryRun = true;
}

if (!isset($config)) {
    Routine::printLog("Define correct configuration file.");
    exit(1);
}

Routine::run($config, $workingDir);

