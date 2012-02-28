<?php
/**
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage
 * @copyright  {copyright}
 * @license    {license_link}
 */

require realpath(dirname(dirname(dirname(__DIR__)))) . '/dev/tests/static/framework/bootstrap.php';
$fileTableNameAssociation = dirname(__FILE__)
        . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "table_names.php";
$tablesAssociation = include_once($fileTableNameAssociation);

define('USAGE', <<<USAGE
$>./factory_table_names.php [-ehrl]
    additional parameters:
    -r          make replacement (default option)
    -l          list of table names not in association list
    -e          output with errors during replacement
    -h          print usage
USAGE
);

$shortOpts = 'ehrl';
$options = getopt($shortOpts);

if (isset($options['h'])) {
    print USAGE;
    exit(1);
}

$outputWithErrors = isset($options['e'])? true: false;
$phpFiles = Util_Files::getPhpFiles();

$makeReplacement = (isset($options['l']) && !isset($options['r']))?
        false : replaceTableNames($phpFiles, $tablesAssociation, $outputWithErrors);
$getTableNamesNotInList = isset($options['l'])?
        getTableNamesNotInList($phpFiles, $tablesAssociation) : false;


function replaceTableNames(array &$files, array &$tablesAssociation, $outputWithErrors)
{
    $errors = array();
    foreach (array_keys($files) as $filePath) {
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
                if (in_array($tableName, array_keys($tablesAssociation))) {
                    $search[] = $tableName;
                    $replace[] = $tablesAssociation[$tableName];
                } else {
                    $errors[] = sprintf("Table name {%s} is not determined.", $tableName);
                }
            }

            if (!empty($replace) && !empty($search)) {
                $content = file_get_contents($filePath);
                $newContent = str_replace($search, $replace, $content);
                if ($newContent != $content) {
                    echo "{$filePath}\n";
                    print_r($tables);
                    file_put_contents($filePath, $newContent);
                }
            }
        }
    }

    if (!empty($errors) && $outputWithErrors) {
        echo "Errors: \n" . implode("\n", $errors) . "\n";
    }
    echo "\n\n";
}

function getTableNamesNotInList(array &$files, array &$tablesAssociation)
{
    $search = array();
    $skippedList = array(
        'c', 'l', 'sc', 'cat_pro', 'table_name', 'rule_customer', 'sales_flat_', 'catalog_product_link_attribute_',
        'catalog_category_flat_', 'catalog_category_entity_', 'catalog_product_flat_', 'catalog_product_entity_',
        'price_index', 'invitation', 'entity_attribute', 'directory_currency', 'sales_bestsellers_aggregated_'
    );
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
}

