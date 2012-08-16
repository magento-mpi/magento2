#!/usr/bin/php
<?php
/**
 * Magento jstest script
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

define('SYNOPSIS', <<<SYNOPSIS
php -f jstests.php -- --config_file "<path_to_file>"

SYNOPSIS
);

/**
 * Parse command line arguments
 */
$currentArgName = false;
$args = array();
foreach ($_SERVER['argv'] as $argNameOrValue) {
    if (substr($argNameOrValue, 0, 2) == '--') {
        // argument name
        $currentArgName = substr($argNameOrValue, 2);
        // in case if argument doesn't need a value
        $args[$currentArgName] = true;
    } else {
        // argument value
        if ($currentArgName) {
            $args[$currentArgName] = $argNameOrValue;
        }
        $currentArgName = false;
    }
}

if (!isset($args['config_file'])) {
    echo SYNOPSIS;
    exit(1);
}
$JsTestDriver = $args['JsTestDriver'];

$baseDir = getcwd();
$configFile = $args['config_file'];
$configFile = file_exists($configFile) ? $configFile : "$configFile.dist";
$config = require($configFile);

$testFilesPath = isset($config['test']) ? $config['test'] : array();
$testFiles = listFiles($testFilesPath);

$loadFilesPath = isset($config['load']) ? $config['load'] : array();
$loadFiles = listFiles($loadFilesPath);
if (empty($loadFiles)) {
    echo "Can not find any files to load";
    exit;
}

$server = isset($config['server']) ? $config['server'] : array();

$fileOrder = $baseDir . "/dev/tests/js/jsTestDriverDependencyOrder.conf";
if (file_exists($fileOrder)) {

    $loadOrder = require($fileOrder);
    $order = isset($loadOrder['loadOrder']) ? $loadOrder['loadOrder'] : array();

    $sortedFiles = array();
    foreach ($order as $k => $v) {
        $sortedFiles[$k] = $v;
    }

    foreach ($loadFiles as $k => $v) {
        $found = false;
        foreach ($order as $key => $value) {
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

$temp_file = "jsTestDriver.prop";
$fh = fopen($temp_file, 'w');
fwrite($fh, "server: $server\r\n");
fwrite($fh, "load:\r\n");
foreach ($sortedFiles as $file) {
    fwrite($fh, "  -  " . str_replace($baseDir, '', $file) . "\r\n");
}
fwrite($fh, "test:\n");
foreach ($testFiles as $file) {
    fwrite($fh, "  -  " . str_replace($baseDir, '', $file) . "\r\n");
}
fclose($fh);

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {

    system('del ' . $baseDir . '\dev\tests\js\test-output\*.* /Q');
    system('rmdir ' . $baseDir . '\dev\tests\js\test-output');
    system('md ' . $baseDir . '\dev\tests\js\test-output');

    $command = 'java -jar ' . $JsTestDriver . ' --config ' . $baseDir . '/' . $temp_file . ' --port 9876 --browser "' . getEnv("firefox") . '" --tests all --testOutput ' . $baseDir . '\tests\js\test-output ';
    echo $command;
    echo '\n';
    system($command);
} else {

    system('kill -9 $( lsof -i:9876 -t )') ;

    $XVFB = system('which Xvfb');
    if (!$XVFB) {
        echo "Xvfb not found.";
        exit;
    }


    $FIREFOX = system('which firefox');
    if (!$FIREFOX) {
        echo "Firefox not found.";
        exit;
    }

    system('$XVFB :99 -ac &'); # launch virtual framebuffer into the background
    system('export DISPLAY=:99'); # set display to use that of the xvfb

    $command = 'java -jar ' . $JsTestDriver . ' --config ' . $baseDir . '/' . $temp_file . ' --port 9876 --browser "' . $FIREFOX . '" --tests all --testOutput ' . $baseDir . '\tests\js\test-output ';
    echo $command;

    system($command);

    system("pkill Xvfb"); # shut down xvfb (firefox will shut down cleanly by JsTestDriver)
    echo "Done.";

}
//@unlink($baseDir . '/' . $temp_file );

function listFiles($dir)
{
    echo getcwd();

    $baseDir = getcwd();
    $result = Array();
    foreach ($dir as $value) {

        echo $path = $baseDir . $value;
        echo "\n";
        if (is_dir($path)) {
            $result = array_merge($result, recDir($path));
        } else if (is_file($path) && preg_match('/\.js$/i', $path)) {
            array_push($result, $path);
        }
    }
    return $result;
}

function recDir($dir)
{
    if ($dh = opendir($dir)) {

        $files = Array();
        $inner_files = Array();

        while ($file = readdir($dh)) {
            if ($file != "." && $file != ".." && $file[0] != '.') {
                if (is_dir($dir . "/" . $file)) {
                    $inner_files = recDir($dir . "/" . $file);
                    if (is_array($inner_files)) $files = array_merge($files, $inner_files);
                } else if (preg_match('/\.js$/i', $file)) {
                    array_push($files, $dir . "/" . $file);
                }
            }
        }
        closedir($dh);
        return $files;
    }
}


?>