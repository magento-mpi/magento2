<?php
/**
 * Magento product edition maker script
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define('USAGE', 'USAGE: php -f edition.php -- --dir="<working_directory>" --edition="<ce|ee>" [--build] [--additional="<dev_build_ce.txt>"]');
try {
    $options = getopt('', array('dir:', 'edition:', 'build', 'additional:'));
    assertCondition(isset($options['dir']), USAGE);
    $dir = $options['dir'];
    assertCondition($dir && is_dir($dir), "The specified directory doesn't exist: {$options['dir']}");
    assertCondition(isset($options['edition']), USAGE);

    $lists = array('common.txt');
    $isBuild = isset($options['build']);
    if (!$isBuild) {
        $lists[] = 'dev_build.txt';
    } elseif (isset($options['additional'])) {
        $lists[] = $options['additional'];
    }
    $gitCmd = sprintf('git --git-dir %s --work-tree %s', escapeshellarg("{$dir}/.git"), escapeshellarg($dir));
    switch ($options['edition']) {
        case 'ce':
            $lists[] = 'ee.txt';
            executeCLI("{$gitCmd} mv CHANGELOG_CE.md CHANGELOG.md");
            break;
        case 'ee':
            executeCLI("{$gitCmd} mv app/etc/enterprise/module.xml.dist app/etc/enterprise/module.xml");
            break;
        default:
            throw new Exception("Specified edition '{$options['edition']}' is not implemented.");
    }

    // remove files that do not belong to edition
    $command = 'php -f ' . __DIR__ . '/../extruder.php -- -v -w ' . escapeshellarg($dir);
    foreach ($lists as $list) {
        $command .= ' -l ' . escapeshellarg(__DIR__ . '/extruder/' . $list);
    }
    executeCLI($command, 'Extruder execution failed');

    // root composer.json
    $command = "php -f " . __DIR__ . '/../../tools/Magento/Tools/Composer/create-root.php --'
        . ' --source-dir=' . escapeshellarg($dir)
        . ' --target-file=' . escapeshellarg($dir . '/composer.json');
    executeCLI($command);
    executeCLI("{$gitCmd} add composer.json");

    // composer.lock becomes outdated, once the composer.json has changed
    $composerLock = $dir . '/composer.lock';
    if (file_exists($composerLock)) {
        executeCLI("{$gitCmd} rm composer.lock");
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
 * Runs a command via CLI
 *
 * @param string $command
 * @param string $error
 * @return void
 */
function executeCLI($command, $error = 'Command has returned non-zero code')
{
    echo $command . PHP_EOL;
    passthru($command, $exitCode);
    assertCondition(!$exitCode, $error);
}
