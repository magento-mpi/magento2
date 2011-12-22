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
$options = getopt('', array(
    'source:', 'target:', 'source-branch::', 'target-branch::', 'target-dir::', 'commit-message::', 'no-push'
));
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

try {
    // determine commit message from changelog
    $commitMsg = file_get_contents(__DIR__ . '/changelog.txt');
    $commitMsg = explode('------', $commitMsg);
    $commitMsg = trim($commitMsg[0]);
    if (empty($commitMsg)) {
        throw new Exception('No commit message found in changelog.');
    }

    // clone target and merge source into it
    execVerbose('git clone %s %s', $targetRepository, $targetDir);
    exec("$gitCmd log -1", $output);
    if ($commitMsg == getOriginalCommitMessage($output)) {
        throw new Exception('The last commit message in the target repository is the same as the last entry'
    	    . " in changelog.txt file. Most likely you forgot to update the changelog.txt):\n\n{$commitMsg}"
    	);
    }
    execVerbose("$gitCmd remote add source %s", $sourceRepository);
    execVerbose("$gitCmd fetch source");
    execVerbose("$gitCmd checkout $targetBranch");
    execVerbose("$gitCmd merge --squash --strategy-option=theirs source/$sourceBranch");
    // workaround for not tracking removed files when merging with '--no-commit' or '--squash', seems to be a Git bug
    execVerbose("$gitCmd diff --name-only -z source/$sourceBranch | xargs -0 -r $gitCmd rm -f");

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
        'php -f %s -- -w %s -c %s -v -0',
        "$licenseToolDir/license-tool.php",
        $targetDir,
        "$licenseToolDir/conf/ce.php"
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
        throw new Exception("Command is passed with error code: ". $exitCode);
    }
}

/**
 * Parse a git log entry and find the commit message from it
 *
 * The returned message is trimmed.
 *
 * @param array $output Output returned in second argument of exec()
 * @return string
 */
function getOriginalCommitMessage($output)
{
    $message = '';
    do {
	$fragment = array_shift($output);
    } while ($fragment != ''); // the fragment with empty string is a divider between meta info and commit message
    foreach ($output as $fragment) {
	$message .= substr($fragment, 4); // each line of the message is crippled with 4 spaces in the beginning
    }
    return trim($message);
}
