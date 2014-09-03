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
define(
    'SYNOPSIS',
<<<SYNOPSIS
php -f prepare_publication.php --
    --source="<repository>" --source-point="<branch name or commit ID>"
    --target="<repository>" [--target-branch="<branch>"] [--target-dir="<directory>"]
    --changelog-file="<markdown_file>"
SYNOPSIS
);
$options = getopt('', array(
    'source:', 'source-point:', 'target:', 'target-branch::', 'target-dir::', 'changelog-file:'
));
if (empty($options['source']) || empty($options['source-point']) || empty($options['target'])
    || empty($options['changelog-file'])) {
    echo SYNOPSIS;
    exit(1);
}

require_once(__DIR__ . '/functions.php');

$sourceRepository = $options['source'];
$targetRepository = $options['target'];
$sourcePoint = $options['source-point'];
$targetBranch = isset($options['target-branch']) ? $options['target-branch'] : 'master';
$targetDir = (isset($options['target-dir']) ? $options['target-dir'] : __DIR__ . '/target');
$changelogFile = $options['changelog-file'];

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

    echo 'Parsing top section of CHANGELOG.md:' . PHP_EOL;
    $commitMsg = trim(getTopMarkdownSection($sourceLog));
    echo $commitMsg . PHP_EOL;

    // replace license notices
    $licenseToolDir = __DIR__ . '/license';
    execVerbose(
        'php -f %s -- -w %s -e ce -v -0',
        "$licenseToolDir/license-tool.php",
        $targetDir
    );

    // commit
    execVerbose("$gitCmd add --update");
    execVerbose("$gitCmd status");
    execVerbose("$gitCmd config user.name " . getGitUsername());
    execVerbose("$gitCmd config user.email " . getGitEmail());
    execVerbose("$gitCmd commit --message=%s", $commitMsg);

} catch (Exception $exception) {
    echo $exception->getMessage() . PHP_EOL;
    exit(1);
}
