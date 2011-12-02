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
    --source="<repository>" [--source-branch="<branch>"]
    --target="<repository>" [--target-branch="<branch>"] [--target-dir="<directory>"]
    [--no-push]

SYNOPSIS
);
$options = getopt('', array('source:', 'target:', 'source-branch::', 'target-branch::', 'target-dir::', 'no-push'));
if (empty($options['source']) || empty($options['target'])) {
    echo SYNOPSIS;
    exit(1);
}

$sourceRepository = $options['source'];
$targetRepository = $options['target'];
$sourceBranch = isset($options['source-branch']) ? $options['source-branch'] : 'master';
$targetBranch = isset($options['target-branch']) ? $options['target-branch'] : 'master';
$targetDir = (isset($options['target-dir']) ? $options['target-dir'] : __DIR__ . '/target');
$canPush = !isset($options['no-push']);

$gitCmd = sprintf('git --git-dir %s --work-tree %s', escapeshellarg("$targetDir/.git"), escapeshellarg($targetDir));

// clone target and merge source into it
execVerbose('git clone %s %s', $targetRepository, $targetDir);
execVerbose("$gitCmd remote add source %s", $sourceRepository);
execVerbose("$gitCmd fetch source");
execVerbose("$gitCmd checkout $targetBranch");
execVerbose("$gitCmd merge --squash --strategy-option=theirs source/$sourceBranch");
// workaround for not tracking removed files when merging with '--no-commit' or '--squash', seems to be a Git bug
execVerbose("$gitCmd diff --name-only -z source/$sourceBranch | xargs -0 -r $gitCmd rm -f");

// remove files that must not be published
$extruderDir = __DIR__ . '/extruder';
execVerbose(
    'php -f %s -- -w %s -l %s -l %s -g -v',
    "$extruderDir/extruder.php",
    $targetDir,
    "$extruderDir/common.txt",
    "$extruderDir/ce.txt"
);

// replace license notices
$licenseToolDir = __DIR__ . '/license';
execVerbose(
    'php -f %s -- -w %s -c %s -v -0',
    "$licenseToolDir/license-tool.php",
    $targetDir,
    "$licenseToolDir/conf/ce.php"
);

// commit and push
execVerbose("$gitCmd add --update");
execVerbose("$gitCmd status");
execVerbose("$gitCmd commit --message=%s", 'Merged commits from the original repository.');
if ($canPush) {
    execVerbose("$gitCmd push origin $targetBranch");
}

/**
 * Execute a command with automatic escaping of arguments
 *
 * @param string $command
 */
function execVerbose($command)
{
    $args = func_get_args();
    $args = array_map('escapeshellarg', $args);
    $args[0] = $command;
    $command = call_user_func_array('sprintf', $args);
    echo $command . "\n";
    passthru($command, $exitCode);
    echo "\n";
    if (0 !== $exitCode) {
        exit(1);
    }
}
