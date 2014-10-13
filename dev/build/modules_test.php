#!/usr/bin/php
<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

require __DIR__ . '/../../app/autoload.php';
(new \Magento\Framework\Autoload\IncludePath())->addIncludePath(__DIR__ . '/../../lib/internal');

define(
    'USAGE',
    "Usage: php -f modules_test.php -- [--list-modules] [--list-file]
    additional parameters:
    --help              print usage message
    --list-modules      list of modules to enable in this format:
                        Module_ModuleName1,Module_ModuleName2,etc.
    --list-file         relative path of file containing list of modules to enable
    \n"
);

$opt = getopt(
    '',
    [
        'help',
        'list-modules:',
        'list-file:',
    ]
);

if (empty($opt) || isset($opt['help'])) {
    echo USAGE;
    exit(1);
}

$tempList = array();
$modulesEnable = array();
$modulesInstalled = array();
$modulesRemove = array();

try {
    $magentoDirectory = __DIR__ . '/../../app/code/Magento/';

    // list-modules and list-file arguments
    if ($opt['list-modules'] == false && $opt['list-file'] == false) {
        throw new Exception("One of parameters --list-modules or --list-file required and cannot be empty.");
    }

    // Get modules to enable
    if ($opt['list-modules']) {
        $tok = strtok($opt['list-modules'], ",");

        while ($tok !== false) {
            array_push($tempList, $tok);
            $tok = strtok(",");
        }
    }

    if ($opt['list-file']) {
        $profileDir = __DIR__ . "/" . $opt['list-file'];
        $handle = @fopen($profileDir, "r");
        if ($handle) {
            while (($buffer = fgets($handle, 4096)) !== false) {
                array_push($tempList, $buffer);
            }
            if (!feof($handle)) {
                throw new Exception("Problem reading test profile file.");
            }
            fclose($handle);
        }
    }

    $prefix = "Magento_";
    foreach ($tempList as $module) {
        $module = trim($module);
        if (substr($module, 0, strlen($prefix)) == $prefix) {
            $module = substr($module, strlen($prefix));
            array_push($modulesEnable, $module);
        }
    }

    // Get modules currently installed
    $shell = new \Magento\Framework\Shell(new \Magento\Framework\Shell\CommandRenderer());
    $command = 'ls ' . $magentoDirectory;
    $tempList = $shell->execute($command);
    if (!$tempList) {
        throw new Exception("Problem finding Magento module directory.");
    }
    $tok = strtok($tempList, " \n");

    while ($tok !== false) {
        array_push($modulesInstalled, $tok);
        $tok = strtok(" \n");
    }

    // Get modules to remove
    $modulesRemove = array_diff($modulesInstalled, $modulesEnable);

    // Removing un-needed modules
    foreach ($modulesRemove as $module) {
        $directory = $magentoDirectory . $module;
        if (!file_exists($directory)) {
            throw new Exception("The file or directory '{$directory} is marked for deletion, but it doesn't exist.");
        }
        $command = 'rm -rf ' . $directory;
        $result = $shell->execute($command);
        if ($result) {
            throw new Exception("Problem removing Magento module directory.");
        }
    }

    exit(0);
} catch (Exception $e) {
    if ($e->getPrevious()) {
        $message = (string)$e->getPrevious();
    } else {
        $message = $e->getMessage();
    }
    echo "\nError: " . $message . "\n\n";

    exit(1);
}
