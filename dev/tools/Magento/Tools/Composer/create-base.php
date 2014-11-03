<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

use Magento\Tools\Composer\Package\Package;
use \Magento\Tools\Composer\Helper\ReplaceFilter;

require __DIR__ . '/../../../bootstrap.php';
$destinationDir = __DIR__ . '/_base';
$sourceDir = realpath(BP);

/**
 * Base Composer Package Creator Tool
 *
 * This tool creates a base composer package
 */
define(
    'USAGE',
    "Usage: php -f create-base.php --"
    . " [--source] [-destination] [--wildcard] [--set=<option:value>]
    --source -source directory. Default value $sourceDir
        this directory must contain a root composer.json which is going to be used as template.
    --destination - destination directory. Default value $destinationDir
    --wildcard - whether to set 'require' versions to wildcard
    --set='path->to->node:value' - set a value to the specified node. Use colon to separate node path and value.
        Overrides anything that was before in the template or in default values.
        May be used multiple times to specify multiple values. For example:
        --set=name:vendor/package --set=extra->branch-alias->dev-master:2.0-dev\n"
);

$opt = getopt('', ['usage', 'source::', 'destination::', 'wildcard', 'set::']);

try {
    if (isset($opt['usage'])) {
        echo USAGE;
        exit(0);
    }

    $sourceDir = isset($opt['source']) ? $opt['source'] : $sourceDir;
    $sourceDir = str_replace('\\', '/', realpath($sourceDir));
    if (!$sourceDir || !is_dir($sourceDir)) {
        throw new Exception($opt['source'] . " must be a Magento code base.");
    }

    $destinationDir = isset($opt['destination']) ? $opt['destination'] : $destinationDir;
    $destinationDir = str_replace('\\', '/', $destinationDir);

    $logWriter = new \Zend_Log_Writer_Stream('php://output');
    $logWriter->setFormatter(new \Zend_Log_Formatter_Simple('[%timestamp%] : %message%' . PHP_EOL));
    $logger = new Zend_Log($logWriter);
    $logger->setTimestampFormat("H:i:s");
    $logger->info(sprintf("Your Magento installation directory (Source Directory): %s ", $sourceDir));
    $logger->info(sprintf("Your base output directory (Destination Directory): %s ", $destinationDir));

    try {
        if (!is_dir($destinationDir)) {
            mkdir($destinationDir, 0777, true);
        }
    } catch (\Exception $ex) {
        $logger->error(sprintf("ERROR: Creating Directory %s failed. Message: %s", $destinationDir, $ex->getMessage()));
        exit($e->getCode());
    }

    // generate composer.json
    // read the initial root composer.json
    $sourceComposer = $sourceDir . '/composer.json';
    if (!is_file($sourceComposer)) {
        $logger->error(sprintf("The source composer.json file doesn't exist: %s", $sourceComposer));
        exit(1);
    }
    $package = new Package(json_decode(file_get_contents($sourceComposer)), $sourceComposer);

    $basePackage = new Package(new \StdClass(), null);
    $basePackage->set('name', 'magento/product-community-edition');
    $basePackage->set('description', 'eCommerce Platform for Growth (Community Edition)');
    $basePackage->set('type', $package->get('type'));
    $basePackage->set('version', $package->get('version'));
    $basePackage->set('repositories', [['type' => 'composer', 'url' => 'http://packages.magento.com/']]);

    // add magento components to require section
    $replaceFilter = new ReplaceFilter($sourceDir);
    $replaceFilter->removeMissing($package);
    $useWildcard = isset($opt['wildcard']);
    $rootWildcard = preg_replace('/\.\d+$/', '.*', $package->get('version'));
    $components['magento/skeleton'] = $useWildcard ? $rootWildcard : 'self.version';
    $components = array_merge($components, $replaceFilter->getMagentoComponentsFromReplace($package, $useWildcard));
    $basePackage->set('require', $components);

    // override with "set" option
    if (isset($opt['set'])) {
        foreach ((array)$opt['set'] as $row) {
            if (!preg_match('/^(.*?):(.+)$/', $row, $matches)) {
                $logger->error(sprintf("preg_match('/^(.*?):(.+)$/', $row, $matches): %s", $row));
                exit(1);
            } else {
                list(, $key, $value) = $matches;
                $basePackage->set($key, $value);
            }
        }
    }

    $basePackage->set('license', ['OSL-3.0', 'AFL-3.0']);

    // output
    $output = $basePackage->getJson();
    $file = $destinationDir . '/composer.json';
    if (!file_put_contents($file, $output)) {
        $logger->error(sprintf("Unable to record composer.json output to the file: %s", $file));
        exit(1);
    }
    $logger->info(sprintf("Composer.json output has been recorded to: %s ", $file));

    // generate README.md
    $readme = $sourceDir . '/README.md';
    $destReadme = $destinationDir . '/README.md';
    if (is_file($readme) && copy($readme, $destReadme)) {
        $logger->info(sprintf("README.md has been copied to: %s", $destReadme));
    } else {
        $logger->error('README.md is not created.');
        exit(1);
    }

    // generate .gitignore
    $destGitignore = $destinationDir . '/.gitignore';
    if (file_put_contents($destGitignore, '/.idea')) {
        $logger->info(sprintf(".gitignore has been created: %s ", $destGitignore));
    } else {
        $logger->error('Unable to create .gitignore');
        exit(1);
    }

    $logger->info(
        sprintf(
            "SUCCESS: Base created! You should be able to find it at %s \n",
            $destinationDir
        )
    );
} catch (\Exception $e) {
    echo $e;
    exit(1);
}