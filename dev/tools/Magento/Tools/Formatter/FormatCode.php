<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter;

use PHPParser_Parser;
use PHPParser_Error;

require_once __DIR__ . '/../../../PHP-Parser/lib/bootstrap.php';
require_once __DIR__ . '/FileUtils.php';
require_once __DIR__ . '/PrettyPrinter.php';
require_once __DIR__ . '/ParserLexer.php';
$parser = new PHPParser_Parser(new ParserLexer());
/**
 * This method returns if the passed in filename matches anything the blacklist.
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
 */
function moveFileSpecifications($sourceItems, &$destinationItems)
{
    // loop through each item to place the normalized reference in the list
    foreach ($sourceItems as $sourceItem) {
        array_push($destinationItems, normalizeDirectorySeparators($sourceItem));
    }
}
/**
 * This method parses the arguments to passed into the program.
 */
function parseArguments($arguments, &$fileItems, &$blacklistItems, &$rootDirectory, &$displayOnly, &$quiet)
{
    foreach ($arguments as $argument) {
        $argumentParameters = array();
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
 */
function fixFile($filename, $rootDirectory)
{
    global $parser;
    printMessage('Reading: ' . getReference($filename, $rootDirectory));
    $prettyPrinter = new PrettyPrinter();
    $code = file_get_contents($filename);
    // parse
    $stmts = $parser->parse($code);
    // pretty print
    $code = '<?php' . PrettyPrinter::EOL . $prettyPrinter->prettyPrint($stmts) . PrettyPrinter::EOL;
    printMessage('Writing: ' . getReference($filename, $rootDirectory));
    file_put_contents($filename, $code);
}
/**
 * This method fixes the named file.
 */
function fixFileCsFixer($filename)
{
    // generate the command line:
    //   php php-cs-fixer.phar fix /path/to/project --level=all
    $commandLine = 'php php-cs-fixer.phar fix ' . $filename . ' --level=all';
    // execute the command
    system($commandLine);
}
# holds the local root directory
$rootDirectory = '.';
# holds the blacklisted items
$blacklistItems = array();
# holds the list of items to format
$fileItems = array();
$displayOnly = false;
# app name is first in the list, drop it since we don't want to format ourselves
array_shift($argv);
// process all arguments passed into the application
parseArguments($argv, $fileItems, $blacklistItems, $rootDirectory, $displayOnly, $quiet);
// holds all of the files that will eventually be addressed
$files = array();
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
    } catch (PHPParser_Error $e) {
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
