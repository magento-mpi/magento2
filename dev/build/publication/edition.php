<?php
/**
 * Magento product edition maker script
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

define('USAGE', 'php -f edition.php -- --dir="<working_directory>" --edition="<ce|ee>" [--build]' . PHP_EOL);
try {
    $options = getopt('', array('dir:', 'edition:', 'build'));
    if (!isset($options['dir']) || !isset($options['edition'])) {
        throw new Exception(USAGE);
    }
    $lists = array('common.txt');
    $isBuild = isset($options['build']);
    if (!$isBuild) {
        $lists[] = 'dev_build.txt';
    }
    switch ($options['edition']) {
        case 'ce':
            $lists[] = 'ee.txt';
            $lists[] = 'saas.txt';
            break;
        case 'ee':
            $lists[] = 'saas.txt';
            break;
        default:
            throw new Exception("Specified edition '{$options['edition']}' is not implemented.");
    }
    $command = 'php -f ' . __DIR__ . '/../extruder.php -- -v -w ' . escapeshellarg($options['dir']);
    foreach ($lists as $list) {
        $command .= ' -l ' . escapeshellarg(__DIR__ . '/extruder/' . $list);
    }
    echo $command . PHP_EOL;
    passthru($command);
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(1);
}
