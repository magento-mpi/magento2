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

// User for Git, which will be the author of the commit
define('GIT_USERNAME', 'mage2-team');
define('GIT_EMAIL', 'mage2-team@magento.com');

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

    $logFile = $targetDir . '/' . $changelogFile;
    echo "Source log file is '$logFile'" . PHP_EOL;
    $targetLog = file_exists($logFile) ? file_get_contents($logFile) : '';

    // copy new & override existing files in the working tree and index from the source repository
    execVerbose("$gitCmd checkout $sourcePoint -- .");
    // remove files that don't exist in the source repository anymore
    $files = execVerbose("$gitCmd diff --name-only $sourcePoint");
    foreach ($files as $file) {
        execVerbose("$gitCmd rm -f %s", $file);
    }

    // remove files that must not be published
    execVerbose('php -f %s -- --dir=%s --edition=ce', __DIR__ . '/edition.php', $targetDir);

    // compare if changelog is different from the published one, compose the commit message
    if (!file_exists($logFile)) {
        throw new Exception("Changelog file '$logFile' does not exist.");
    }
    $sourceLog = file_get_contents($logFile);
    if (!empty($targetLog) && $sourceLog == $targetLog) {
        throw new Exception("Aborting attempt to publish with old changelog. '$logFile' is not updated.");
    }

    $commitMsg = trim(getTopMarkdownSection($sourceLog));

    // replace license notices
    $licenseToolDir = __DIR__ . '/license';
    execVerbose(
        'php -f %s -- -w %s -e ce -v -0',
        "$licenseToolDir/license-tool.php",
        $targetDir
    );

    // composer.json
    copy(__DIR__ . '/composer.json_', $targetDir . '/composer.json');
    execVerbose("$gitCmd add composer.json");

    // commit and push
    execVerbose("$gitCmd add --update");
    execVerbose("$gitCmd status");
    execVerbose("$gitCmd config user.name " . GIT_USERNAME);
    execVerbose("$gitCmd config user.email " . GIT_EMAIL);
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
 * @throws Exception
 * @link http://daringfireball.net/projects/markdown/syntax
 */
function getTopMarkdownSection($contents)
{
    $parts = preg_split('/^[=\-]+\s*$/m', $contents);
    if (!isset($parts[1])) {
        throw new Exception("No commit message found in the changelog file.");
    }
    list($title, $body) = $parts;
    if (!preg_match("/^\d(.\d+){3}[\w-.]*$/", $title)) {
        throw new Exception("No version found on top of the changelog file.");
    }
    $body = explode("\n", trim($body));
    array_pop($body);
    $body = implode("\n", $body);
    return $title . $body;
}
