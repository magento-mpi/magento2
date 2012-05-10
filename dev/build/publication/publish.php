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
    --source="<repository>" --source-point="<branch name or commit ID>"
    --target="<repository>" [--target-branch="<branch>"] [--target-dir="<directory>"]
    --changelog-file="<markdown_file>"
    [--no-push]

SYNOPSIS
);
$options = getopt('', array(
    'source:', 'source-point:', 'target:', 'target-branch::', 'target-dir::', 'changelog-file:', 'no-push'
));
if (empty($options['source']) || empty($options['source-point']) || empty($options['target'])
    || empty($options['changelog-file'])) {
    echo SYNOPSIS;
    exit(1);
}

$sourceRepository = $options['source'];
$targetRepository = $options['target'];
$sourcePoint = $options['source-point'];
$targetBranch = isset($options['target-branch']) ? $options['target-branch'] : 'master';
$targetDir = (isset($options['target-dir']) ? $options['target-dir'] : __DIR__ . '/target');
$changelogFile = $options['changelog-file'];
$canPush = !isset($options['no-push']);

$gitCmd = sprintf('git --git-dir %s --work-tree %s', escapeshellarg("$targetDir/.git"), escapeshellarg($targetDir));

try {
    // clone target repository and attach the source repo as a remote
    execVerbose('git clone %s %s', $targetRepository, $targetDir);
    execVerbose("$gitCmd remote add source %s", $sourceRepository);
    execVerbose("$gitCmd fetch source");
    execVerbose("$gitCmd checkout $targetBranch");

    // determine whether source-point is a branch name or a commit ID
    try {
        $sourceBranch = "source/$sourcePoint";
        execVerbose("$gitCmd rev-parse $sourceBranch");
        $sourcePoint = $sourceBranch;
    } catch (Exception $e) {
        echo "Assuming that 'source-point' is a commit ID." . PHP_EOL;
    }

    // compare if changelog is different from the published one, compose the commit message
    $projectRootDir = realpath(__DIR__ . '/../../../');
    $sourceLogFile = $projectRootDir . DIRECTORY_SEPARATOR . $changelogFile;
    $targetLogFile = $targetDir . DIRECTORY_SEPARATOR . $changelogFile;
    if (!file_exists($sourceLogFile)) {
        throw new Exception("Changelog file '$sourceLogFile' does not exist.");
    }
    $sourceLog = file_get_contents($sourceLogFile);
    if (file_exists($targetLogFile) && $sourceLog == file_get_contents($targetLogFile)) {
        throw new Exception("Aborting attempt to publish with old changelog."
            . " Contents of these files are not supposed to be equal: '$sourceLogFile' and '$targetLogFile'."
        );
    }
    $commitMsg = trim(getTopMarkdownSection($sourceLog));
    if (empty($commitMsg)) {
        throw new Exception("No commit message found in the changelog file '$sourceLogFile'.");
    }

    // copy new & override existing files in the working tree and index from the source repository
    execVerbose("$gitCmd checkout $sourcePoint -- .");
    // remove files that don't exist in the source repository anymore
    $files = execVerbose("$gitCmd diff --name-only $sourcePoint");
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
 * @throws Exception
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
        throw new Exception("Command has failed with exit code: $exitCode.");
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
