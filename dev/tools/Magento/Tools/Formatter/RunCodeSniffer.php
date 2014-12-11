<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
/**
 * This is used to execute the code sniffer on the same files modified by the formatter.
 */
// php \_xtools\PHP_CodeSniffer\scripts\phpcs -p -n --standard=PSR2 --report-xml=results.xml --extensions=php
//    --ignore=<contents of blacklist file> <contents of files file>
$fileUtilsFile = dirname(__FILE__) . '/FileUtils.php';
if (file_exists($fileUtilsFile)) {
    include $fileUtilsFile;
}
/**
 * This method adds the options specified in the array.
 *
 * @param string $optionMarker String used to deliniate the option
 * @param array $options Array of options to add to the command line
 * @param string &$commandLine String containing the command line to execute
 * @return void
 */
function addOptions($optionMarker, $options, &$commandLine)
{
    if (sizeof($options) > 0) {
        foreach ($options as $key => $value) {
            $commandLine .= $optionMarker;
            $commandLine .= $key;
            if ($value) {
                $commandLine .= '=';
                $commandLine .= $value;
            }
        }
    }
}
# app name is first in the list, drop it since we don't need it
array_shift($argv);
$singleDashOptions = [];
$doubleDashOptions = [];
// add defaults
$singleDashOptions['p'] = null;
$singleDashOptions['n'] = null;
$doubleDashOptions['standard'] = 'PSR2';
$doubleDashOptions['report-xml'] = 'results.xml';
$doubleDashOptions['extensions'] = 'php';
// determine the best guess at where the blacklist lives based on current file and current working directory
$rootDirectory = getcwd();
$fileRoot = __DIR__;
if (startsWith($fileRoot, $rootDirectory)) {
    $fileRoot = substr($fileRoot, strlen($rootDirectory) + 1);
}
$fileRoot = normalizeDirectorySeparators($fileRoot);
$filelistFileName = joinPaths($fileRoot, 'files.txt');
$blacklistFileName = joinPaths($fileRoot, 'blacklist.txt');
$phpcsFileName = '\\_xtools\\PHP_CodeSniffer\\scripts\\phpcs';
// loop through each parameter and put them on the command line
foreach ($argv as $parameter) {
    if ('-' === $parameter[0]) {
        // remove the dash from the parameter
        $parameter = substr($parameter, 1);
        // if this is a double dash, just store it
        if ('-' === $parameter[0]) {
            // remove the dash from the parameter
            $parameter = substr($parameter, 1);
            // just store the parameter
            parse_str($parameter, $outputs);
            foreach ($outputs as $key => $value) {
                $doubleDashOptions[$key] = $value;
            }
        } else {
            // just store the parameter
            parse_str($parameter, $outputs);
            foreach ($outputs as $key => $value) {
                if ('blacklist' === $key) {
                    $blacklistFileName = $value;
                } elseif ('filelist' === $key) {
                    $filelistFileName = $value;
                } elseif ('phpcs' === $key) {
                    $phpcsFileName = $value;
                } else {
                    $singleDashOptions[$key] = $value;
                }
            }
        }
    } else {
        echo 'Ignoring parameter: ' . $parameter . PHP_EOL;
    }
}
// read in the files and directories to ignore
$ignores = getLines($blacklistFileName);
// add each ignore to the command line
if (sizeof($ignores) > 0) {
    $ignoreList = '';
    foreach ($ignores as $ignoreNumber => $ignore) {
        if ($ignoreNumber > 0) {
            $ignoreList .= ',';
        }
        $ignoreList .= $ignore;
    }
    $doubleDashOptions['ignore'] = $ignoreList;
}
// build the command
$commandLine = 'php ';
$commandLine .= $phpcsFileName;
addOptions(' -', $singleDashOptions, $commandLine);
addOptions(' --', $doubleDashOptions, $commandLine);
// read in the files and directories to act on
$files = getLines($filelistFileName);
// add each file to the command line
if (sizeof($files) > 0) {
    foreach ($files as $fileNumber => $file) {
        $commandLine .= ' ';
        $commandLine .= $file;
    }
}
echo PHP_EOL;
echo 'Executing command: ' . PHP_EOL;
echo $commandLine . PHP_EOL . PHP_EOL;
system($commandLine);
