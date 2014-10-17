<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

define(
    'USAGE',
    "Usage: php -f remove-unwanted-modules.php -- --list-file module_list_file\n"
);

$opt = getopt(
    '',
    [
        'list-file:',
    ]
);

if (empty($opt)) {
    echo USAGE;
    exit(1);
}

$tempList = array();
$modulesEnable = array();
$modulesInstalled = array();
$modulesToRemove = array();

try {
    $magentoBaseDirectory = dirname(dirname(__DIR__));
    $magentoCodeDirectory = $magentoBaseDirectory . '/app/code/Magento';

    $moduleListFile = $opt['list-file'];
    if (!is_file($moduleListFile)) {
        throw new Exception("The specified module list file does not exist: " . $moduleListFile);
    }
    $modules = file($moduleListFile);

    foreach ($modules as $module) {
        $modulesEnable[] = explode('_', trim($module))[1];
    }

    // Get modules currently installed
    $modulesInstalled = array_diff(scandir($magentoCodeDirectory), array('..', '.'));
    if (!$modulesInstalled) {
        throw new Exception("Problem finding Magento module directory.");
    }

    // Get modules to remove
    $modulesToRemove = array_diff($modulesInstalled, $modulesEnable);

    // Removing un-needed modules
    foreach ($modulesToRemove as $module) {
        $directory = $magentoCodeDirectory . DIRECTORY_SEPARATOR . $module;
        $result = deleteDirectory($directory);
        if (!$result) {
            throw new Exception(
                "The file or directory '{$directory}' could not be deleted."
            );
        }
        echo "Removed module " . $module . "\n";
    }

    // update module.xml file to move the modules
    $moduleFile = $magentoBaseDirectory . '/app/etc/enterprise/module.xml';
    if (is_file($moduleFile) && !empty($modulesToRemove)) {
        $modules = '(' . implode($modulesToRemove, '|') . ')';
        $pattern = "/^.*" . $modules . ".*$/m";
        $contents = file_get_contents($moduleFile);
        $contents = preg_replace($pattern, '', $contents);
        file_put_contents($moduleFile, $contents);
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

/**
 * @param string $dir
 * @return bool
 */
function deleteDirectory($dir)
{
    if (!file_exists($dir)) {
        return true;
    }
    if (!is_dir($dir) || is_link($dir)) {
        return unlink($dir);
    }

    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }
        if (!deleteDirectory($dir . "/" . $item)) {
            chmod($dir . "/" . $item, 0777);
            if (!deleteDirectory($dir . "/" . $item)) {
                return false;
            }
        };
    }

    return rmdir($dir);
}
