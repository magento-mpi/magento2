<?php
/**
 * Magento run-js-tests script. This script executes all Magento JavaScript unit tests.
 *
 * {license_notice}
 *
 * @category    tests
 * @package     js
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once 'lib/Varien/Io/Interface.php';
require_once 'lib/Varien/Io/Abstract.php';
require_once 'lib/Varien/Io/File.php';

$options = getopt("", array("configFile:", "jsTestDriver:"));
if (count($options) != 2) {
    reportError('Usage: php -f run-js-tests.php -- --configFile "<path to file>" --jsTestDriver "<path to jar file>"');
}

$configFile = $options["configFile"];
if (!file_exists($configFile)) {
    reportError('Configuration file does not exist: ' . $configFile);
}
$jsTestDriver = $options["jsTestDriver"];
if (!file_exists($jsTestDriver)) {
    reportError('JsTestDriver jar file does not exist: ' . $jsTestDriver);
}

$baseDir = getcwd();

$config = require($configFile);

$server = isset($config['server']) ? $config['server'] : "http://localhost:9876";
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

$fileOrder = $baseDir . '/dev/tests/js/jsTestDriverOrder.php';
if (file_exists($fileOrder)) {
    $loadOrder = require($fileOrder);
    foreach ($loadOrder as $k => $v) {
        $sortedFiles[$k] = $v;
    }
    foreach ($loadFiles as $k => $v) {
        $found = false;
        foreach ($loadOrder as $key => $value) {
            if (strcmp($baseDir . $value, $v) == 0) {
                $found = true;
                break;
            }
        }
        if ($found == false) {
            array_push($sortedFiles, $v);
        }
    }
}

$jsTestDriverConf = "jsTestDriver.conf";
$fh = fopen($jsTestDriverConf, 'w');

fwrite($fh, "server: $server" . PHP_EOL);

fwrite($fh, "proxy:" . PHP_EOL);
foreach ($proxies as $proxy) {
    $proxyServer = sprintf($proxy['server'], $server);
    fwrite($fh, '  - {matcher: "' . $proxy['matcher'] . '", server: "' . $proxyServer . '"}' . PHP_EOL);
}

fwrite($fh, "load:" . PHP_EOL);
foreach ($sortedFiles as $file) {
    if (!in_array($file, $serveFiles)) {
        fwrite($fh, "  - " . str_replace($baseDir, '', $file) . PHP_EOL);
    }
}

fwrite($fh, "test:" . PHP_EOL);
foreach ($testFiles as $file) {
    fwrite($fh, "  - " . str_replace($baseDir, '', $file) . PHP_EOL);
}

fwrite($fh, "serve:" . PHP_EOL);
foreach ($serveFiles as $file) {
    fwrite($fh, "  - " . str_replace($baseDir, '', $file) . PHP_EOL);
}

fclose($fh);

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    Varien_Io_File::rmdirRecursive($baseDir . '\dev\tests\js\test-output');
    mkdir($baseDir . '\dev\tests\js\test-output', 0777, true);

    $command
        = 'java -jar ' . $jsTestDriver . ' --config ' . $baseDir . '/' . $jsTestDriverConf . ' --port 9876 --browser "'
        . getEnv("firefox") . '" --tests all --testOutput ' . $baseDir . '\dev\tests\js\test-output ';

    echo $command;
    system($command);
} else {
    Varien_Io_File::rmdirRecursive($baseDir . '/dev/tests/js/test-output');
    mkdir($baseDir . '/dev/tests/js/test-output', 0777, true);

    $command
        = 'java -jar ' . $jsTestDriver . ' --config ' . $baseDir . '/' . $jsTestDriverConf . ' --port 9876 --browser "'
        . exec('which firefox') . '" --tests all --testOutput ' . $baseDir . '/dev/tests/js/test-output ';

    $shellCommand
        = '#!/bin/bash
        kill -9 $(/usr/sbin/lsof -i:9876 -t )
        pkill Xvfb
        XVFB=`which Xvfb`
        if [ "$?" -eq 1 ];
        then
            echo "Xvfb not found."
            exit 1
        fi

        FIREFOX=`which firefox`
        if [ "$?" -eq 1 ];
        then
            echo "Firefox not found."
            exit 1
        fi

        $XVFB :99 -ac &    # launch virtual framebuffer into the background
        PID_XVFB="$!"      # take the process ID
        export DISPLAY=:99 # set display to use that of the xvfb

        # run the tests
        ' . $command . '

        kill $PID_XVFB     # shut down xvfb (firefox will shut down cleanly by JsTestDriver)
        echo "Done."';

    system($shellCommand);
}

/**
 * Reports an error given an error message and exits, effectively halting the PHP script's execution.
 *
 * @param $message - Error message to be displayed to the user.
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
 * @param $dir - An array of directories as specified in the configuration file (i.e. $configFile).
 *
 * @return array - An array of directory paths to all Javascript files found by recursively searching the
 * specified array of directories.
 */
function listFiles($dir)
{
    $baseDir = getcwd();
    $result = array();
    foreach ($dir as $value) {
        $path = $baseDir . $value;
        $result = array_merge(
            $result, listFiles(str_replace($baseDir, '', glob($path . '/*', GLOB_ONLYDIR | GLOB_NOSORT)))
        );
        $result = array_merge($result, glob($path . '/*.js'));
    }
    return $result;
}
