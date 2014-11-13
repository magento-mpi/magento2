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
    --target-satis-repo="<repository>" [--target-satis-dir="<directory>"]
    --target-product-repo="<repository>" [--target-product-dir="<directory>"]
SYNOPSIS
);
$options = getopt('', array(
        'source-dir:', 'changelog-file:', 'target-satis-repo:', 'target-satis-dir::',
        'target-product-repo:', 'target-product-dir::'
    ));
$requiredArgs = ['source-dir', 'changelog-file', 'target-satis-repo', 'target-product-repo'];
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
$productTargetDir = (isset($options['target-product-dir']) ?
    $options['target-product-dir'] :
    __DIR__ . '/_product');
$productTargetRepo = $options['target-product-repo'];

try {
    $gitSatisCmd = sprintf(
        'git --git-dir %s --work-tree %s',
        escapeshellarg("$satisTargetDir/.git"),
        escapeshellarg($satisTargetDir)
    );
    $gitProductCmd = sprintf(
        'git --git-dir %s --work-tree %s',
        escapeshellarg("$productTargetDir/.git"),
        escapeshellarg($productTargetDir)
    );

    // prepare base
    $sourceBaseDir = __DIR__ . '/_tmp_base_source';
    execVerbose("git clone %s %s", $sourceDir, $sourceBaseDir);

    //prepare product repo
    execVerbose("git clone $productTargetRepo $productTargetDir");
    $dir = dir($productTargetDir);
    while (false !== ($file = $dir->read())) {
        if (in_array($file, ['.', '..', '.git', '.gitignore'])) {
            continue;
        }
        execVerbose("$gitProductCmd rm -r $file");
    }

    //create product directory
    $readmeFile = $sourceBaseDir . '/README.md';
    if (is_file($readmeFile)) {
        copy($readmeFile, $productTargetDir . '/README.md');
    }

    //create product root composer.json
    $targetComposerJson = $productTargetDir . '/composer.json';
    execVerbose(
        'php -f ' . __DIR__
        . '/../../tools/Magento/Tools/Composer/create-root.php -- --type=product --source-dir=%s --target-file=%s',
        $sourceBaseDir,
        $targetComposerJson
    );

    //create base root composer.json
    $targetComposerJson = $sourceBaseDir . '/composer.json';
    execVerbose(
        'php -f ' . __DIR__
        . '/../../tools/Magento/Tools/Composer/create-root.php -- --type=base --source-dir=%s --target-file=%s',
        $sourceBaseDir,
        $targetComposerJson
    );
    $rootJson = json_decode(file_get_contents($targetComposerJson));

    // init satis repo
    execVerbose("git clone $satisTargetRepo $satisTargetDir");

    // generate all packages
    execVerbose(
        'php -f ' . __DIR__ . '/../../tools/Magento/Tools/Composer/archiver.php -- '
        . "--dir=$sourceBaseDir --output=$satisTargetDir/_packages"
    );

    //remove product zip package if exist
    execVerbose("rm -f $satisTargetDir/_packages/magento_product-*");

    // commit changes to satis repo
    execVerbose("$gitSatisCmd add .");
    execVerbose("$gitSatisCmd config user.name " . getGitUsername());
    execVerbose("$gitSatisCmd config user.email " . getGitEmail());
    execVerbose("$gitSatisCmd commit -m 'Updated packages [version: $rootJson->version]'");

    // Commit changes to product repo
    $logFile = $sourceDir . '/' . $changelogFile;
    echo "Source log file is '$logFile'" . PHP_EOL;
    $sourceLog = file_get_contents($logFile);
    $commitMsg = trim(getTopMarkdownSection($sourceLog));
    if (!preg_match('#^' . preg_quote($rootJson->version) . '\n#', $commitMsg)) {
        throw new \UnexpectedValueException(
            "Version on top of Changelog doesn't correspond to the release version '$rootJson->version'"
        );
    }
    execVerbose("$gitProductCmd add .");
    execVerbose("$gitProductCmd config user.name " . getGitUsername());
    execVerbose("$gitProductCmd config user.email " . getGitEmail());
    execVerbose("$gitProductCmd commit -m %s", $commitMsg);
    execVerbose("$gitProductCmd tag $rootJson->version");
} catch (Exception $exception) {
    echo $exception->getMessage() . PHP_EOL;
    exit(1);
}
