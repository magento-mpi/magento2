#!/usr/bin/php
<?php
/**
 * {license_notice}
 *
 * @category   build
 * @package    license
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Command line tool for processing file docblock of Magento source code files.
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

try {
    Routine::run($config, $workingDir);
} catch(Exception $e) {
    Routine::printLog($e->getMessage());
    exit(1);
}
