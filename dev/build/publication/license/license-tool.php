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
php -f license-tool.php -- -e <edition> [-w <dir>] [-v] [-d] [-0]
    -e <edition> name of product edition (see "conf" directory relatively to this script)
    -w <dir>     use specified working dir instead of current
    -v           verbose output
    -d           dry run
    -0           exit with a zero status even when not all replacements have succeeded

USAGE
);

$options = getopt('e:w:vd0');

if (!isset($options['e'])) {
    print USAGE;
    exit(1);
}

if (isset($options['v'])) {
    Routine::$isVerbose = true;
}

$dryRun = false;
if (isset($options['d'])) {
    Routine::$dryRun = true;
}

$workingDir = '.';
if (isset($options['w'])) {
    $workingDir = rtrim($options['w'], DIRECTORY_SEPARATOR);
}
if (!is_dir($workingDir)) {
    Routine::printLog('Working dir "' . $workingDir . '" does not exist');
    exit(1);
}

$config = include __DIR__ . "/conf/{$options['e']}.php";
if (defined('EDITION_LICENSE')) {
    foreach ($config as $path => $settings) {
        foreach ($settings as $type => $license) {
            if ('_params' == $type) {
                continue;
            }
            if ('OSL' == $license || 'AFL' == $license) {
                $config[$path][$type] = EDITION_LICENSE;
            }
        }
    }
}

$blackList = include __DIR__ . '/conf/blacklist.php';

try {
    Routine::run($workingDir, $config, $blackList);
} catch(Exception $e) {
    Routine::printLog($e->getMessage());
    exit(isset($options['0']) ? 0 : 1);
}
