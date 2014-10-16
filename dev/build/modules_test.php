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
    $magentoCodeDirectory = __DIR__ ;
    $parts = explode('/', $magentoCodeDirectory);
    array_pop($parts);
    array_pop($parts);
    $magentoCodeDirectory = implode('/', $parts);
    $magentoCodeDirectory = $magentoCodeDirectory . '/app/code/Magento/';

    // list-modules and list-file arguments
    if ((isset($opt['list-modules']) == false) && (isset($opt['list-file']) == false)) {
        throw new Exception("One of parameters --list-modules or --list-file required and cannot be empty.");
    }

    // Get modules to enable
    if (isset($opt['list-modules'])) {
        $tok = strtok($opt['list-modules'], ",");

        while ($tok !== false) {
            array_push($tempList, $tok);
            $tok = strtok(",");
        }
    }

    if (isset($opt['list-file'])) {
        $profileDir = $opt['list-file'];
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
    $modulesInstalled = array_diff(scandir($magentoCodeDirectory), array('..', '.'));
    if (!$modulesInstalled) {
        throw new Exception("Problem finding Magento module directory.");
    }

    // Get modules to remove
    $modulesRemove = array_diff($modulesInstalled, $modulesEnable);

    // Removing un-needed modules
    foreach ($modulesRemove as $module) {
        $directory = $magentoCodeDirectory . $module;
        $result = deleteDirectory($directory);
        if (!$result) {
            throw new Exception("The file or directory '{$directory}' is marked for deletion, but it doesn't exist or
                could not be deleted.");
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

function deleteDirectory($dir)
{
    if (!file_exists($dir)) {
        return true;
    }
    if (!is_dir($dir) || is_link($dir)) {
        return unlink($dir);
    }

    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..')
            continue;
        if (!deleteDirectory($dir . "/" . $item)) {
            chmod($dir . "/" . $item, 0777);
            if (!deleteDirectory($dir . "/" . $item))
                return false;
        };
    }

    return rmdir($dir);
}