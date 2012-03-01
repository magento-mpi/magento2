<?php
/**
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage
 * @copyright  {copyright}
 * @license    {license_link}
 */

define('USAGE', <<<USAGE
$>./factory_table_names.php [-ehds]
    additional parameters:
    -d          replacement in dry-run mode
    -s          search for table names not in list for replacement
    -e          output with errors during replacement
    -h          print usage
USAGE
);

$shortOpts = 'ehds';
$options = getopt($shortOpts);

if (isset($options['h'])) {
    print USAGE;
    exit(1);
}

$outputWithErrors = isset($options['e']);
$isDryRunMode = isset($options['d']);
$isSearchTables = isset($options['s']);

require realpath(dirname(dirname(dirname(__DIR__)))) . '/dev/tests/static/framework/bootstrap.php';
$tablesAssociation = include_once(dirname(__FILE__) . '/factory_table_names/replace.php');

$phpFiles = Util_Files::getPhpFiles();



$replacementResult = false;
if (!$isSearchTables) {
    $replacementResult = replaceTableNames($phpFiles, $tablesAssociation, $outputWithErrors, $isDryRunMode);
} else if($isSearchTables && $isDryRunMode) {
    $replacementResult = replaceTableNames($phpFiles, $tablesAssociation, $outputWithErrors, $isDryRunMode);
}

$searchResult = $isSearchTables? searchTableNamesNotInReplacedList($phpFiles, $tablesAssociation) : false;

if ($replacementResult || $searchResult) {
    exit(1);
}
exit(0);

/**
 * Check if file in /app/code directory or contain template extension
 * Avoiding checking additional files
 *
 * @param $filePath
 * @return bool
 */
function isFileShouldBeReplaced($filePath)
{
    return (false !== strpos(str_replace('\\', '/', $filePath), '/app/code/'))
            && ('phtml' != pathinfo($filePath, PATHINFO_EXTENSION));
}

/**
 * Replace table names in all files
 *
 * @param array $files
 * @param array $tablesAssociation
 * @param $outputWithErrors
 * @param $isDryRunMode
 * @return bool
 */
function replaceTableNames(array &$files, array &$tablesAssociation, $outputWithErrors, $isDryRunMode)
{
    $isErrorsFound = false;
    $errors = array();
    foreach (array_keys($files) as $filePath) {
        if (!isFileInAppCode($filePath)) {
            continue;
        }

        $search = $replace = array();

        $tables = Legacy_TableTest::extractTables($filePath);
        $tables = array_filter(
            $tables,
            function ($table) {
                return false !== strpos($table['name'], '/');
            }
        );

        if (!empty($tables)) {
            foreach ($tables as $table) {
                $tableName = $table['name'];
                if (isset($tablesAssociation[$tableName])) {
                    $search[] = $tableName;
                    $replace[] = $tablesAssociation[$tableName];
                } else {
                    $errors[] = $tableName;
                }
            }

            if (!empty($replace) && !empty($search)) {
                replaceTableNamesInFile($filePath, $search, $replace, $isDryRunMode);
            }
            if (!empty($errors)) {
                if ($outputWithErrors) {
                   echo "Error - Missed table names in config: \n" . implode(", ", $errors) . "\n";
                }
                $errors = array();
                $isErrorsFound = true;
            }
        }
    }

    return $isErrorsFound;
}

/**
 * Replace table names in an file
 *
 * @param $filePath
 * @param $search
 * @param $replace
 * @param $isDryRunMode
 */
function replaceTableNamesInFile($filePath, $search, $replace, $isDryRunMode)
{
    $content = file_get_contents($filePath);
    $newContent = str_replace($search, $replace, $content);
    if ($newContent != $content) {
        echo "{$filePath}\n";
        echo 'Replaced tables: '; print_r($search);
        if (!$isDryRunMode) {
            file_put_contents($filePath, $newContent);
        }
    }
}

/**
 * Looking for table names which not defined in current config
 *
 * @param array $files
 * @param array $tablesAssociation
 * @return bool
 */
function searchTableNamesNotInReplacedList(array &$files, array &$tablesAssociation)
{
    $search = array();
    $skippedList = include_once(dirname(__FILE__) . '/factory_table_names/blacklist.php');
    foreach (array_keys($files) as $filePath) {
        $tables = Legacy_TableTest::extractTables($filePath);
        foreach ($tables as $table) {
            if (in_array($table['name'], $skippedList)) {
                continue;
            }
            if (!in_array($table['name'], array_values($tablesAssociation)) && !in_array($table['name'], $search)) {
                $search[] = $table['name'];
            }
        }
    }

    if (!empty($search)) {
        echo "List of table names not in association list: \n";
        print_r(array_unique($search));
    }

    return false;
}
