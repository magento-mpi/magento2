#!/usr/bin/php
<?php
/**
 * Magento install script
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

define(
    'SYNOPSIS',
<<<SYNOPSIS
php -f install.php -- --build_properties_file "<path_to_file>"

SYNOPSIS
);

/**
 * Parse command line arguments
 */
$currentArgName = false;
$args = array();
foreach ($_SERVER['argv'] as $argNameOrValue) {
    if (substr($argNameOrValue, 0, 2) == '--') {
        // argument name
        $currentArgName = substr($argNameOrValue, 2);
        // in case if argument doesn't need a value
        $args[$currentArgName] = true;
    } else {
        // argument value
        if ($currentArgName) {
            $args[$currentArgName] = $argNameOrValue;
        }
        $currentArgName = false;
    }
}

if (!isset($args['build_properties_file'])) {
    echo SYNOPSIS;
    exit(1);
}
$baseDir = realpath(__DIR__ . '/../../../');
$configFile = $args['build_properties_file'];
$configFile = file_exists($configFile) ? $configFile : "$configFile.dist";
$config = require($configFile);
$installOptions = isset($config['install_options']) ? $config['install_options'] : array();

$reportDir = __DIR__ . '/' . $config['report_dir'];

/* Install application */
if ($installOptions) {
    $installCmd = sprintf('php -f %s --', escapeshellarg("$baseDir/dev/shell/install.php"));
    foreach ($installOptions as $optionName => $optionValue) {
        $installCmd .= sprintf(' --%s %s', $optionName, escapeshellarg($optionValue));
    }

    passthru($installCmd, $exitCode);
    if ($exitCode) {
        exit($exitCode);
    }
}

require_once __DIR__ . '/../../../app/bootstrap.php';
$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);
/** @var \Magento\Indexer\App\Indexer $app */
$app = $bootstrap->createApplication('Magento\Indexer\App\Indexer', ['reportDir' => $reportDir]);
$bootstrap->run($app);
