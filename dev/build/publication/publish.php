#!/usr/bin/php
<?php
/**
 * Magento repository publishing script
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

// get CLI options, define variables
define('SYNOPSIS', <<<SYNOPSIS
php -f publish.php --
    --source="<repository>" [--source-branch="<branch>"] [--source-commit="<commit>"]
    --target="<repository>" [--target-branch="<branch>"] [--target-dir="<directory>"]
    [--no-push]

SYNOPSIS
);
$options = getopt('', array(
    'source:', 'target:', 'source-branch::', 'source-commit::', 'target-branch::', 'target-dir::', 'no-push'
));
if (empty($options['source']) || empty($options['target'])) {
    echo SYNOPSIS;
    exit(1);
}

$sourceRepository = $options['source'];
$targetRepository = $options['target'];
$sourceBranch = isset($options['source-branch']) ? $options['source-branch'] : 'master';
$source = empty($options['source-commit']) ? "source/{$sourceBranch}" : $options['source-commit'];
$targetBranch = isset($options['target-branch']) ? $options['target-branch'] : 'master';
$targetDir = (isset($options['target-dir']) ? $options['target-dir'] : __DIR__ . '/target');
$canPush = !isset($options['no-push']);

$gitCmd = sprintf('git --git-dir %s --work-tree %s', escapeshellarg("$targetDir/.git"), escapeshellarg($targetDir));

try {
    // clone target and merge source into it
    execVerbose('git clone %s %s', $targetRepository, $targetDir);
    execVerbose("$gitCmd remote add source %s", $sourceRepository);
    execVerbose("$gitCmd fetch source");
    execVerbose("$gitCmd checkout $targetBranch");

    // compare if changelog is different from current
    $sourceLogFile = realpath(__DIR__ . '/../../../CHANGELOG.markdown');
    $log = file_get_contents($sourceLogFile);
    $targetLogFile = realpath($targetDir . '/CHANGELOG.markdown');
    if ($targetLogFile && $log == file_get_contents($targetLogFile)) {
        throw new Exception("Aborting attempt to publish with old changelog."
            . " Contents of these files are not supposed to be equal: {$sourceLogFile} and {$targetLogFile}"
        );
    }
    $commitMsg = trim(getTopMarkdownSection($log));
    if (empty($commitMsg)) {
        throw new Exception('No commit message found in changelog.');
    }

    // Copy files from source repository to our working tree and index
    execVerbose("$gitCmd checkout {$source} -- .");
    // Additional command to remove files, deleted in source repository, as they are not removed by 'git checkout'
    $files = execVerbose("$gitCmd diff --name-only {$source}");
    foreach ($files as $file) {
        execVerbose("$gitCmd rm -f %s", $file);
    }

    // remove files that must not be published
    $extruderDir = __DIR__ . '/extruder';
    execVerbose(
        'php -f %s -- -w %s -l %s -l %s -l %s -g -v',
        "$extruderDir/extruder.php",
        $targetDir,
        "$extruderDir/common.txt",
        "$extruderDir/common_tests.txt",
        "$extruderDir/ce.txt"
    );

    // replace license notices
    $licenseToolDir = __DIR__ . '/license';
    execVerbose(
        'php -f %s -- -w %s -e ce -v -0',
        "$licenseToolDir/license-tool.php",
        $targetDir
    );

    // commit and push
    execVerbose("$gitCmd add --update");
    execVerbose("$gitCmd status");
    execVerbose("$gitCmd commit --message=%s", $commitMsg);
    if ($canPush) {
        execVerbose("$gitCmd push origin $targetBranch");
    }
} catch (Exception $exception) {
    echo $exception->getMessage() . PHP_EOL;
    exit(1);
}

/**
 * Execute a command with automatic escaping of arguments
 *
 * @param string $command
 * @return array
 */
function execVerbose($command)
{
    $args = func_get_args();
    $args = array_map('escapeshellarg', $args);
    $args[0] = $command;
    $command = call_user_func_array('sprintf', $args);
    echo $command . PHP_EOL;
    exec($command, $output, $exitCode);
    foreach ($output as $line) {
        echo $line . PHP_EOL;
    }
    if (0 !== $exitCode) {
        throw new Exception("Command is passed with error code: ". $exitCode);
    }
    return $output;
}

/**
 * Get the top section of a text in markdown format
 *
 * @param string $contents
 * @return string
 * @link http://daringfireball.net/projects/markdown/syntax
 */
function getTopMarkdownSection($contents)
{
    $parts = preg_split('/^[=\-]+\s*$/m', $contents);
    if (!isset($parts[1])) {
        return '';
    }
    list($title, $body) = $parts;
    $body = explode("\n", trim($body));
    array_pop($body);
    $body = implode("\n", $body);
    return $title . $body;
}
