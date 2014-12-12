<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter;

use DateTime;
use Magento\Tools\Formatter\PrettyPrinter\Printer;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../../../PHP-Parser/lib/bootstrap.php';
require_once __DIR__ . '/FileUtils.php';
require_once __DIR__ . '/PrettyPrinter.php';
require_once __DIR__ . '/ParserLexer.php';

/**
 * This method returns if the passed in filename matches anything the blacklist.
 *
 * @param string $fileName
 * @param string[] $blacklistItems
 * @return bool
 */
function fileAcceptable($fileName, $blacklistItems)
{
    $acceptable = true;
    // loop through all the items in the blacklist to see if the filename matches any of them
    foreach ($blacklistItems as $blacklistItem) {
        $acceptable = strpos($fileName, $blacklistItem) === false;
        if (!$acceptable) {
            break;
        }
    }
    return $acceptable;
}

/**
 * This method moves items from one list to another, normalizing the directory separators as it goes.
 *
 * @param string[] $sourceItems
 * @param string[] &$destinationItems
 * @return void
 */
function moveFileSpecifications($sourceItems, &$destinationItems)
{
    // loop through each item to place the normalized reference in the list
    foreach ($sourceItems as $sourceItem) {
        array_push($destinationItems, normalizeDirectorySeparators($sourceItem));
    }
}

/**
 * This method parses the arguments passed into the program.
 *
 * @param string[] $arguments
 * @param string[] &$fileItems
 * @param string[] &$blacklistItems
 * @param string &$rootDirectory
 * @param bool &$displayOnly
 * @param bool &$quiet
 * @return void
 */
function parseArguments($arguments, &$fileItems, &$blacklistItems, &$rootDirectory, &$displayOnly, &$quiet)
{
    foreach ($arguments as $argument) {
        $argumentParameters = [];
        if (preg_match('/^--?q(?:uiet)?$/', $argument)) {
            $quiet = true;
            continue;
        } elseif (preg_match('/^--?b(?:lacklist)?=(.*)$/', $argument, $argumentParameters)) {
            $blacklistFileName = $argumentParameters[1];
            echo 'Blacklist file: ' . $blacklistFileName . PHP_EOL;
            moveFileSpecifications(getLines($blacklistFileName), $blacklistItems);
            continue;
        } elseif (preg_match('/^--?f(?:ilelist)?=(.*)$/', $argument, $argumentParameters)) {
            $filelistFileName = $argumentParameters[1];
            echo 'File list: ' . $filelistFileName . PHP_EOL;
            moveFileSpecifications(getLines($filelistFileName), $fileItems);
            continue;
        } elseif (preg_match('/^--?r(?:oot)?=(.*)$/', $argument, $argumentParameters)) {
            $rootDirectory = normalizeDirectorySeparators($argumentParameters[1]);
            echo 'Root directory: ' . $rootDirectory . PHP_EOL;
            continue;
        } elseif (preg_match('/^--?d(?:isplay)?$/', $argument)) {
            $displayOnly = true;
            echo 'Display only: ' . $displayOnly . PHP_EOL;
            continue;
        }
        // add anything else to the file list
        array_push($fileItems, normalizeDirectorySeparators($argument));
    }
}

// flag indicating verbosity of application
$quiet = false;

/**
 * This method prints out a message, but will surpress if the quiet flag is turned on
 *
 * @param string $message
 * @return void
 */
function printMessage($message)
{
    global $quiet;
    if (!$quiet) {
        echo $message . PHP_EOL;
    }
}

/**
 * This method returns the relative path of the file.
 *
 * @param string $filename
 * @param string $rootDirectory
 * @return string
 */
function getReference($filename, $rootDirectory)
{
    $reference = $filename;
    if (startsWith($filename, $rootDirectory)) {
        $reference = substr($filename, strlen($rootDirectory));
    }
    return $reference;
}

/**
 * This method fixes the named file.
 *
 * @param string $filename
 * @param string $rootDirectory
 * @return void
 * @throws \PHPParser_error
 */
function fixFile($filename, $rootDirectory)
{
    $start = new DateTime();
    // read the file into memory
    printMessage('Reading: ' . getReference($filename, $rootDirectory));
    $originalCode = file_get_contents($filename);
    // create a printer with the original code
    $prettyPrinter = new Printer($originalCode);
    try {
        // perform the parsing
        $prettyPrinter->parseCode();
        if ($prettyPrinter->hasChange()) {
            // write out the formatted code
            printMessage('Writing: ' . getReference($filename, $rootDirectory));
            file_put_contents($filename, $prettyPrinter->getFormattedCode());
        } else {
            printMessage('No changes required.');
        }
    } catch (\PHPParser_Error $e) {
        $output = $prettyPrinter->getFormattedCode();
        if (null !== $output) {
            file_put_contents($filename . '.error', $output);
            echo "Invalid code placed in " . $filename . ".error" . PHP_EOL;
            echo 'Parse Error: ', $e->getMessage();
            echo PHP_EOL;
        } else {
            throw $e;
        }
    }
    $stop = new DateTime();
    printMessage('Processing took: ' . $stop->diff($start)->format('%h:%I:%S'));
}

/**
 * This method fixes the named file.
 *
 * @param string $filename
 * @return void
 */
function fixFileCsFixer($filename)
{
    // generate the command line:
    //   php php-cs-fixer.phar fix /path/to/project --level=all
    $commandLine = 'php php-cs-fixer.phar fix ' . $filename . ' --level=all';
    // execute the command
    system($commandLine);
}

# holds the start time of running the application
$startTask = new DateTime();
# holds the local root directory
$rootDirectory = '.';
# holds the blacklisted items
$blacklistItems = [];
# holds the list of items to format
$fileItems = [];
$displayOnly = false;
# app name is first in the list, drop it since we don't want to format ourselves
array_shift($argv);
// process all arguments passed into the application
parseArguments($argv, $fileItems, $blacklistItems, $rootDirectory, $displayOnly, $quiet);
// holds all of the files that will eventually be addressed
$files = [];
// reverse the order of the items so that they are processed in the order in which they were specified
$fileItems = array_reverse($fileItems);
// address each item in the stack
while (sizeof($fileItems) > 0) {
    $fileItem = array_pop($fileItems);
    // only look at the directory if it is not blacklisted
    if (fileAcceptable($fileItem, $blacklistItems)) {
        // add the root if not specified
        if (!startsWith($fileItem, $rootDirectory)) {
            $fileItem = joinPaths($rootDirectory, $fileItem);
        }
        if (is_dir($fileItem)) {
            $fileItems = array_merge($fileItems, glob($fileItem . '/*'));
        } else {
            if (file_exists($fileItem)) {
                if (preg_match('/\\.php$/', $fileItem)) {
                    array_push($files, $fileItem);
                } else {
                    printMessage('Ignoring: ' . getReference($fileItem, $rootDirectory));
                }
            } else {
                printMessage('File could not be found: ' . $fileItem);
            }
        }
    }
}
echo 'file count: ' . count($files) . PHP_EOL;
$fileCount = 0;
$messageSummary = '';
foreach ($files as $filename) {
    try {
        printMessage('File ' . ++$fileCount);
        if ($displayOnly === false) {
            fixFile($filename, $rootDirectory);
        }
    } catch (\PHPParser_Error $e) {
        $messageSummary .= 'In ' . $filename . ': ' . $e->getMessage() . PHP_EOL;
        echo "checkout {$filename}\n";
        echo 'Parse Error: ', $e->getMessage();
        echo PHP_EOL;
    }
}
// display the errors at the end so they are not missed
if (!$quiet && strlen($messageSummary) > 0) {
    echo 'Error found in processing: ' . PHP_EOL;
    echo $messageSummary;
}
$stopTask = new DateTime();
if ($fileCount > 1) {
    printMessage("Processed {$fileCount} files in {$stopTask->diff($startTask)->format('%h:%I:%S')}");
}
