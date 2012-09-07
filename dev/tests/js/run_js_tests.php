<?php
/**
 * This script executes all Magento JavaScript unit tests.
 *
 * {license_notice}
 *
 * @category    tests
 * @package     js
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once __DIR__ . '/../../../lib/Magento/Autoload.php';
Magento_Autoload::getInstance()->addIncludePath('../../../lib');
require_once('Varien/Io/File.php');

$options = getopt("", array("Browser::", "JsTestDriver::"));

$configFile = file_exists('jsTestDriver.php') ? 'jsTestDriver.php' : 'jsTestDriver.php.dist';
$config = require($configFile);

if (array_key_exists('JsTestDriver', $options)) {
    $jsTestDriver = $options['JsTestDriver'];
} elseif (isset($config['JsTestDriver'])) {
    $jsTestDriver = $config['JsTestDriver'];
} else {
    $jsTestDriver = getEnv('JsTestDriver');
    if (!$jsTestDriver) {
        echo "Value for the 'JsTestDriver' configuration parameter is not specified." . PHP_EOL;
        showUsage();
    }
}
if (!file_exists($jsTestDriver)) {
    reportError('JsTestDriver jar file does not exist: ' . $jsTestDriver);
}

if (array_key_exists('Browser', $options)) {
    $browser = $options['Browser'];
} elseif (isset($config['Browser'])) {
    $browser = $config['Browser'];
} else {
    $browser = getEnv('firefox');
    if (!$browser) {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $browser = 'C:\Program Files (x86)\Mozilla Firefox\firefox.exe';
        } else {
            $browser = exec('which firefox');
        }
    }
}
if (!file_exists($browser)) {
    reportError('Browser executable not found: ' . $browser);
}

$server = isset($config['server']) ? $config['server'] : "http://localhost:9876";
$port = substr(strrchr($server, ':'), 1);

$proxies = isset($config['proxy']) ? $config['proxy'] : array();

$testFilesPath = isset($config['test']) ? $config['test'] : array();
$testFiles = listFiles($testFilesPath);

$loadFilesPath = isset($config['load']) ? $config['load'] : array();
$loadFiles = listFiles($loadFilesPath);
if (empty($loadFiles)) {
    reportError('Could not find any files to load.');
}

$serveFilesPath = isset($config['serve']) ? $config['serve'] : array();
$serveFiles = listFiles($serveFilesPath);

$sortedFiles = array();

$fileOrder = 'jsTestDriverOrder.php';
if (file_exists($fileOrder)) {
    $loadOrder = require($fileOrder);
    foreach ($loadOrder as $i => $file) {
        $sortedFiles[$i] = '../../..' . $file;
    }
    foreach ($loadFiles as $loadFile) {
        $found = false;
        foreach ($loadOrder as $orderFile) {
            if (strcmp('../../..' . $orderFile, $loadFile) == 0) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            array_push($sortedFiles, $loadFile);
        }
    }
}

$jsTestDriverConf = "jsTestDriver.conf";
$fh = fopen($jsTestDriverConf, 'w');

fwrite($fh, "server: $server" . PHP_EOL);

fwrite($fh, "proxy:" . PHP_EOL);
foreach ($proxies as $proxy) {
    $proxyServer = sprintf($proxy['server'], $server, str_replace("\\", "/", realpath(__DIR__ . '/../../..')));
    fwrite($fh, '  - {matcher: "' . $proxy['matcher'] . '", server: "' . $proxyServer . '"}' . PHP_EOL);
}

fwrite($fh, "load:" . PHP_EOL);
foreach ($sortedFiles as $file) {
    if (!in_array($file, $serveFiles)) {
        fwrite($fh, "  - " . $file . PHP_EOL);
    }
}

fwrite($fh, "test:" . PHP_EOL);
foreach ($testFiles as $file) {
    fwrite($fh, "  - " . $file . PHP_EOL);
}

fwrite($fh, "serve:" . PHP_EOL);
foreach ($serveFiles as $file) {
    fwrite($fh, "  - " . $file . PHP_EOL);
}

fclose($fh);

$testOutput = 'test-output';
Varien_Io_File::rmdirRecursive($testOutput);
mkdir($testOutput);

$command
    = 'java -jar ' . $jsTestDriver . ' --config ' . $jsTestDriverConf . ' --port ' . $port .
    ' --browser "' . $browser . '" --tests all --testOutput ' . $testOutput;

echo $command . PHP_EOL;

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    system($command);
} else {
    $shellCommand
        = '#!/bin/bash
        kill -9 `/usr/sbin/lsof -i :' . $port . ' -t`
        pkill Xvfb
        XVFB=`which Xvfb`
        if [ "$?" -eq 1 ];
        then
            echo "Xvfb not found."
            exit 1
        fi

        $XVFB :99 -screen 0 1024x768x24 -ac & # launch virtual frame buffer into the background
        PID_XVFB="$!"        # take the process ID
        export DISPLAY=:99.0 # set display to use that of the Xvfb

        # run the tests
        ' . $command . '

        kill $PID_XVFB       # shut down Xvfb (firefox will shut down cleanly by JsTestDriver)
        echo "Done."';

    system($shellCommand);
}

/**
 * Show a message that displays how to use (invoke) this PHP script and exit.
 */
function showUsage()
{
    reportError('Usage: php [-f] run_js_tests.php [-- --Browser=<path to browser> --JsTestDriver=<path to jar file>]');
}

/**
 * Reports an error given an error message and exits, effectively halting the PHP script's execution.
 *
 * @param $message - Error message to be displayed to the user.
 *
 * @SuppressWarnings(PHPMD.ExitExpression)
 */
function reportError($message)
{
    echo $message . PHP_EOL;
    exit(1);
}

/**
 * Accepts an array of directories and generates a list of Javascript files (.js) in those directories and
 * all subdirectories recursively.
 *
 * @param $dirs - An array of directories as specified in the configuration file (i.e. $configFile).
 *
 * @return array - An array of directory paths to all Javascript files found by recursively searching the
 * specified array of directories.
 */
function listFiles($dirs)
{
    $baseDir = '../../..';
    $result = array();
    foreach ($dirs as $dir) {
        $path = $baseDir . $dir;
        $result = array_merge(
            $result, listFiles(str_replace($baseDir, '', glob($path . '/*', GLOB_ONLYDIR | GLOB_NOSORT)))
        );
        $result = array_merge($result, glob($path . '/*.js'));
    }
    return $result;
}
