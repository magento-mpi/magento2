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
php -f publish.php --
    --edition="ce|ee"
    --source-dir="<directory>"
    --changelog-file="<markdown_file>"
    --target-satis-repo="<repository>" [--target-satis-dir=="<directory>"]
    --target-skeleton-repo="<repository>" [--target-skeleton-dir=="<directory>"]
SYNOPSIS
);
$options = getopt('', array(
        'edition:', 'source-dir:', 'changelog-file:', 'target-satis-repo:', 'target-satis-dir::',
        'target-skeleton-repo:', 'target-skeleton-dir::'
    ));
$requiredArgs = ['edition', 'source-dir', 'changelog-file', 'target-satis-repo', 'target-skeleton-repo'];
foreach ($requiredArgs as $arg) {
    if (empty($options[$arg])) {
        echo SYNOPSIS;
        exit(1);
    }
}

require_once(__DIR__ . '/functions.php');

$edition = $options['edition'];
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
    execVerbose("git clone %s %s", $sourceDir, $sourceSkeletonDir);
    execVerbose(
        'php -f ' . __DIR__ . '/../../tools/Magento/Tools/Composer/create-skeleton.php -- --edition=%s --dir=%s',
        $edition,
        $sourceSkeletonDir
    );

    // init satis repo
    execVerbose("git clone $satisTargetRepo $satisTargetDir");
    execVerbose("$gitSatisCmd rm -r _packages");

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
    execVerbose("unzip $satisTargetDir/_packages/magento_product-* -d $skeletonTargetDir");
    execVerbose("rm -f $satisTargetDir/_packages/magento_product-*");

    // Determine version
    $composerFile = $skeletonTargetDir . '/composer.json';
    if (!file_exists($composerFile)) {
        throw new \UnexpectedValueException("Composer file is not found in '$composerFile'");
    }
    $composerConfig = json_decode(file_get_contents($composerFile));
    if (!isset($composerConfig->version)) {
        throw new \UnexpectedValueException("No 'version' information found in '$composerFile'");
    }
    $version = $composerConfig->version;
    echo "Release version is '$version'" . PHP_EOL;

    // commit changes to satis repo
    execVerbose("$gitSatisCmd add .");
    execVerbose("$gitSatisCmd config user.name " . getGitUsername());
    execVerbose("$gitSatisCmd config user.email " . getGitEmail());
    execVerbose("$gitSatisCmd commit -m 'Updated packages [version: $version]'");

    // Commit changes to skeleton repo
    execVerbose("$gitSkeletonCmd add .");
    execVerbose("$gitSkeletonCmd config user.name " . getGitUsername());
    execVerbose("$gitSkeletonCmd config user.email " . getGitEmail());
    $logFile = $sourceDir . '/' . $changelogFile;
    echo "Source log file is '$logFile'" . PHP_EOL;
    $sourceLog = file_get_contents($logFile);
    $commitMsg = trim(getTopMarkdownSection($sourceLog));
    execVerbose("$gitSkeletonCmd commit -m %s", $commitMsg);
    execVerbose("$gitSkeletonCmd tag $version");
} catch (Exception $exception) {
    echo $exception->getMessage() . PHP_EOL;
    exit(1);
}
