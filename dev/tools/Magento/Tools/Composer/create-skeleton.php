<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../bootstrap.php';

use \Magento\Tools\Composer\Creator\ComposerCreator;
use \Magento\Tools\Composer\Model\Package;
use \Magento\Tools\Composer\Helper\Helper;

/**
 * Composer Skeleton Creator Tool
 *
 * This tool creates skeleton composer.json file.
 */
try {
    $opt = new \Zend_Console_Getopt(
        array(
            'edition|e=s' => 'Edition of which packaging is done. Acceptable values: [ee|enterprise] or [ce|community]',
            'verbose|v' => 'Detailed console logs',
            'dir|d=s' => 'Working directory. Default value ' . realpath(BP),
        )
    );
    $opt->parse();

    $workingDir = $opt->getOption('w') ?: realpath(BP);
    if (!$workingDir || !is_dir($workingDir)) {
        throw new Exception($opt->getOption('w') . " must be a Magento code base.");
    }

    $logWriter = new \Zend_Log_Writer_Stream('php://output');
    $logWriter->setFormatter(new \Zend_Log_Formatter_Simple('[%timestamp%] : %message%' . PHP_EOL));
    $logger = new Zend_Log($logWriter);
    $logger->setTimestampFormat('H:i:s');
    $filter = $opt->getOption('v') ?
            new \Zend_Log_Filter_Priority(Zend_Log::DEBUG) :
            new \Zend_Log_Filter_Priority(Zend_Log::INFO);
    $logger->addFilter($filter);

    $edition = $opt->getOption('e') ?: null;
    if (!$edition) {
        $logger->info('Edition is required. Acceptable values [ee|enterprise] or [ce|community]');
        exit(100);
    }

    $product = null;
    switch (strtolower($edition)) {
        case 'enterprise':
        case 'ee':
            $logger->info('Your Edition: Enterprise');
            $product = new Package("magento/product-enterprise", "0.1.0", '', 'project');
            break;
        case 'community':
        case 'ce':
            $logger->info('Your Edition: Community');
            $product = new Package("magento/product-community", "0.1.0", '', 'project');
            break;
        default:
            $logger->info('Edition value not acceptable. Acceptable values: [ee|ce]');
            exit(100);
    }

    $logger->info(sprintf("Your Magento Installation Directory: %s ", $workingDir));

    //Locations to look for components
    $components = Helper::getComponentsList($workingDir);

    $dependencies = array();
    foreach ($components as $component) {
        foreach (glob($component . '/*', GLOB_ONLYDIR) as $dir) {
            $file = $dir . '/composer.json';
            if (!file_exists($file)) {
                $logger->info('composer.json file not found for ' . $file);
                exit(100);
            }
            $json = json_decode(file_get_contents($file));
            $depends = new Package(
                $json->name,
                $json->version,
                $dir,
                ''
            );
            $product->addDependencies($depends);
        }
    }
    $logger->debug(sprintf("Total Dependencies on Skeleton: %s", sizeof($product->getDependencies())));
    $creator = new ComposerCreator($workingDir, $logger);
    $creator->create(array($product));

    $logger->info(sprintf("SUCCESS: Created composer.json for %s edition", $edition));
} catch (\Zend_Console_Getopt_Exception $e) {
    exit($e->getUsageMessage());
} catch (\Exception $e) {
    exit($e->getMessage());
}
