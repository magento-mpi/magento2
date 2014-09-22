#!/usr/bin/php
<?php
/**
 * Script for preparing package repositories of Magento components and product
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
php -f prepare_packages.php --
    --source-dir="<directory>"
    --changelog-file="<markdown_file>"
    --target-satis-repo="<repository>" [--target-satis-dir=="<directory>"]
    --target-skeleton-repo="<repository>" [--target-skeleton-dir=="<directory>"]
SYNOPSIS
);
$options = getopt('', array(
        'source-dir:', 'changelog-file:', 'target-satis-repo:', 'target-satis-dir::',
        'target-skeleton-repo:', 'target-skeleton-dir::'
    ));
$requiredArgs = ['source-dir', 'changelog-file', 'target-satis-repo', 'target-skeleton-repo'];
foreach ($requiredArgs as $arg) {
    if (empty($options[$arg])) {
        echo SYNOPSIS;
        exit(1);
    }
}

require_once(__DIR__ . '/functions.php');

$sourceDir = $options['source-dir'];
$changelogFile = $options['changelog-file'];
$satisTargetDir = (isset($options['target-satis-dir']) ? $options['target-satis-dir'] : __DIR__ . '/_satis');
$satisTargetRepo = $options['target-satis-repo'];
$skeletonTargetDir = (isset($options['target-skeleton-dir']) ?
    $options['target-skeleton-dir'] :
    __DIR__ . '/_skeleton');
$skeletonTargetRepo = $options['target-skeleton-repo'];

try {
    $gitSatisCmd = sprintf(
        'git --git-dir %s --work-tree %s',
        escapeshellarg("$satisTargetDir/.git"),
        escapeshellarg($satisTargetDir)
    );
    $gitSkeletonCmd = sprintf(
        'git --git-dir %s --work-tree %s',
        escapeshellarg("$skeletonTargetDir/.git"),
        escapeshellarg($skeletonTargetDir)
    );

    // prepare skeleton
    $sourceSkeletonDir = __DIR__ . '/_tmp_sekelton_source';
    $targetComposerJson = $sourceSkeletonDir . '/composer.json';
    execVerbose("git clone %s %s", $sourceDir, $sourceSkeletonDir);
    execVerbose(
        'php -f ' . __DIR__
        . '/../../tools/Magento/Tools/Composer/create-root.php -- --skeleton --source-dir=%s --target-file=%s',
        $sourceSkeletonDir,
        $targetComposerJson
    );
    $rootJson = json_decode(file_get_contents($targetComposerJson));

    // init satis repo
    execVerbose("git clone $satisTargetRepo $satisTargetDir");

    // generate all packages
    execVerbose(
        'php -f ' . __DIR__ . '/../../tools/Magento/Tools/Composer/archiver.php -- '
        . "--dir=$sourceSkeletonDir --output=$satisTargetDir/_packages"
    );

    // prepare skeleton repo
    execVerbose("git clone $skeletonTargetRepo $skeletonTargetDir");
    $dir = dir($skeletonTargetDir);
    while (false !== ($file = $dir->read())) {
        if (in_array($file, ['.', '..', '.git'])) {
            continue;
        }
        execVerbose("$gitSkeletonCmd rm -r $file");
    }

    //create skeleton package directory
    execVerbose(
        'php -f ' . __DIR__ . '/../../tools/Magento/Tools/Composer/create-skeleton.php -- '
        . "--source=$sourceSkeletonDir --destination=$skeletonTargetDir"
    );
    //remove product zip package if exist
    execVerbose("rm -f $satisTargetDir/_packages/magento_product-*");

    // commit changes to satis repo
    execVerbose("$gitSatisCmd add .");
    execVerbose("$gitSatisCmd config user.name " . getGitUsername());
    execVerbose("$gitSatisCmd config user.email " . getGitEmail());
    execVerbose("$gitSatisCmd commit -m 'Updated packages [version: $rootJson->version]'");

    // Commit changes to skeleton repo
    execVerbose("$gitSkeletonCmd add .");
    execVerbose("$gitSkeletonCmd config user.name " . getGitUsername());
    execVerbose("$gitSkeletonCmd config user.email " . getGitEmail());
    $logFile = $sourceDir . '/' . $changelogFile;
    echo "Source log file is '$logFile'" . PHP_EOL;
    $sourceLog = file_get_contents($logFile);
    $commitMsg = trim(getTopMarkdownSection($sourceLog));
    if (!preg_match('#^' . preg_quote($rootJson->version) . '\n#', $commitMsg)) {
        throw new \UnexpectedValueException(
            "Version on top of Changelog doesn't correspond to the release version '$rootJson->version'"
        );
    }
    execVerbose("$gitSkeletonCmd commit -m %s", $commitMsg);
    execVerbose("$gitSkeletonCmd tag $rootJson->version");
} catch (Exception $exception) {
    echo $exception->getMessage() . PHP_EOL;
    exit(1);
}
