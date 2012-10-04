<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     performance_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$magentoBaseDir = getOption('basedir');
if (!isset($magentoBaseDir) || !is_dir($magentoBaseDir)) {
    throw new Exception('Magento application base dir is not defined');
}
require_once "$magentoBaseDir/app/bootstrap.php";
Mage::app('admin', 'store');

// Run required indexes - either all or just one
/** @var $indexer Mage_Index_Model_Indexer */
$indexer = Mage::getModel('Mage_Index_Model_Indexer');
$indexCode = getOption('index');
if ($indexCode) {
    $process = $indexer->getProcessByCode($indexCode);
    if (!$process) {
        throw new Exception("Index with code '{$indexCode}' is not found");
    }
    $processes = array($process);
} else {
    $processes = $indexer->getProcessesCollection();
}

foreach ($processes as $process) {
    echo "Reindexing ", $process->getIndexerCode(), PHP_EOL;
    $process->reindexEverything();
}

/**
 * Retrieves a command line option.
 * getopt() cannot be used instead, because testing framework uses underscores in option names, which is not
 * supported by that routine.
 *
 * @param string $option
 * @return null|string
 */
function getOption($option)
{
    global $argv;
    foreach ($argv as $argument) {
        if (preg_match("/--{$option}=(.*)/", $argument, $matches)) {
            return $matches[1];
        }
    }
    return null;
}
