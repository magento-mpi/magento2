<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../bootstrap.php';

use \Magento\Tools\Composer\Package\Reader;
use \Magento\Tools\Composer\Package\Package;

/**
 * Composer Skeleton Creator Tool
 *
 * This tool creates skeleton composer.json file.
 */
try {
    $opt = new \Zend_Console_Getopt(
        array(
            'edition|e=s' => 'Edition of which packaging is done. Acceptable values: [ee|ce]',
            'version|ver=s' => 'Version for the composer.json file',
            'verbose|v' => 'Detailed console logs',
            'dir|d=s' => 'Working directory. Default value ' . realpath(BP),
        )
    );
    $opt->parse();

    $version = $opt->getOption('ver');
    $workingDir = $opt->getOption('d') ?: realpath(BP);
    if (!$workingDir || !is_dir($workingDir)) {
        throw new Exception($opt->getOption('d') . " must be a Magento code base.");
    }

    $logWriter = new \Zend_Log_Writer_Stream('php://output');
    $logWriter->setFormatter(new \Zend_Log_Formatter_Simple('[%timestamp%] : %message%' . PHP_EOL));
    $logger = new Zend_Log($logWriter);
    $logger->setTimestampFormat('H:i:s');
    $filter = $opt->getOption('v') ?
            new \Zend_Log_Filter_Priority(Zend_Log::DEBUG) :
            new \Zend_Log_Filter_Priority(Zend_Log::INFO);
    $logger->addFilter($filter);
    $logger->info('Working copy root directory: ' . $workingDir);

    $edition = $opt->getOption('e') ?: null;
    switch (strtolower($edition)) {
        case 'ee':
            $name = 'magento/product-enterprise-edition';
            break;
        case 'ce':
            $name = 'magento/product-community-edition';
            break;
        default:
            throw new \Exception('Edition value not acceptable. Acceptable values: [ee|ce]');
    }
    $logger->info("Root package name: {$name}");

    $root = new Package(
        json_decode(file_get_contents(__DIR__ . '/etc/root_composer_template.json')),
        $workingDir . '/composer.json'
    );
    $root->getJson()->name = $name;
    if ($version) {
        $root->getJson()->version = $version;
    }
    $reader = new Reader($workingDir);
    foreach ($reader->readMagentoPackages() as $package) {
        $root->setRequire($package->get('name'), $package->get('version'));
    }
    $size = sizeof((array)$root->get('require'));
    $logger->debug("Total number of dependencies in the skeleton package: {$size}");
    file_put_contents(
        $root->getFile(),
        json_encode($root->getJson(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n"
    );
    $logger->info("SUCCESS: created package at {$root->getFile()}");
} catch (\Zend_Console_Getopt_Exception $e) {
    $e->getUsageMessage();
    exit(1);
} catch (\Exception $e) {
    echo $e;
    exit(1);
}
