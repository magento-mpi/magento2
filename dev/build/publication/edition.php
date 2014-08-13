<?php
/**
 * Magento product edition maker script
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define(
    'USAGE',
    'USAGE: php -f edition.php -- --dir="<working_directory>" --edition="<ce|ee>" [--internal]'
);
require __DIR__ . '/functions.php';
try {
    $options = getopt('', array('dir:', 'edition:', 'internal'));
    assertCondition(isset($options['dir']), USAGE);
    $dir = $options['dir'];
    assertCondition($dir && is_dir($dir), "The specified directory doesn't exist: {$options['dir']}");
    $dir = rtrim(str_replace('\\', '/', $dir), '/');
    assertCondition(isset($options['edition']), USAGE);

    $lists = array('no-edition.txt');
    $includeLists = [];

    $baseDir = __DIR__ . '/../../../';
    $isTargetBaseDir = realpath($baseDir) == realpath($dir);
    if (!$isTargetBaseDir) {
        // remove service scripts, if edition tool is run outside of target directory
        $lists[] = 'services.txt';
    } else {
        $includeLists[] = 'services.txt';
    }

    $isInternal = isset($options['internal']) ? true : false;
    if ($isInternal) {
        if ($options['edition'] != 'ee') {
            $lists[] = 'internal_ee.txt';
        } else {
            $includeLists[] = 'internal_ee.txt';
        }
        $includeLists[] = 'internal.txt';
    } else {
        $lists[] = 'internal.txt';
        $lists[] = 'internal_ee.txt';
    }

    $gitCmd = sprintf('git --git-dir %s --work-tree %s', escapeshellarg("{$dir}/.git"), escapeshellarg($dir));
    switch ($options['edition']) {
        case 'ce':
            $lists[] = 'ee.txt';
            copyAll("{$dir}/dev/build/publication/extra_files/ce", $dir);
            break;
        case 'ee':
            $includeLists[] = 'ee.txt';
            $moduleXml = "{$dir}/app/etc/enterprise/module.xml";
            echo "Copy {$moduleXml}.dist to {$moduleXml}\n";
            copy("{$moduleXml}.dist", $moduleXml);
            copyAll("{$dir}/dev/build/publication/extra_files/ee", $dir);
            break;
        default:
            throw new Exception("Specified edition '{$options['edition']}' is not implemented.");
    }

    execVerbose("{$gitCmd} add .");

    // remove files that do not belong to edition
    $command = 'php -f ' . __DIR__ . '/../extruder.php -- -v -w ' . escapeshellarg($dir);
    foreach ($lists as $list) {
        $command .= ' -l ' . escapeshellarg(__DIR__ . '/edition/' . $list);
    }
    foreach ($includeLists as $list) {
        $command .= ' -i ' . escapeshellarg(__DIR__ . '/edition/' . $list);
    }
    execVerbose($command, 'Extruder execution failed');

    // root composer.json
    $command = "php -f " . __DIR__ . '/../../tools/Magento/Tools/Composer/create-root.php --'
        . ' --source-dir=' . escapeshellarg($dir)
        . ' --target-file=' . escapeshellarg($dir . '/composer.json');
    execVerbose($command);
    execVerbose("{$gitCmd} add composer.json");

    // composer.lock becomes outdated, once the composer.json has changed
    $composerLock = $dir . '/composer.lock';
    if (file_exists($composerLock)) {
        execVerbose("{$gitCmd} rm -f -- composer.lock");
    }
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(1);
}

/**
 * A basic assertion
 *
 * @param bool $condition
 * @param string $error
 * @return void
 * @throws \Exception
 */
function assertCondition($condition, $error)
{
    if (!$condition) {
        throw new \Exception($error);
    }
}

/**
 * Copy all files maintaining the directory structure
 *
 * @param string $from
 * @param string $to
 * @return void
 */
function copyAll($from, $to)
{
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($from));
    /** @var SplFileInfo $file */
    foreach ($iterator as $file) {
        if (!$file->isDir()) {
            $source = $file->getPathname();
            $relative = substr($source, strlen($from));
            $dest = $to . $relative;
            $targetDir = dirname($dest);
            if (!is_dir($targetDir)) {
                echo "Mkdir {$targetDir}\n";
                mkdir($targetDir, 0755, true);
            }
            echo "Copy {$source} to {$dest}\n";
            copy($source, $dest);
        }
    }
}
