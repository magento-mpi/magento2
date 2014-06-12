<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Composer Packager Tool
 *
 * This tool create composer.json identifier file for Composer compatibility.
 * The result of this tool execution would be composer.json file at each component folders in Magento.
 */
require __DIR__ . '/../../../bootstrap.php';

use \Magento\Tools\Composer\Extractor\AdminThemeExtractor;
use \Magento\Tools\Composer\Extractor\FrontendThemeExtractor;
use \Magento\Tools\Composer\Creator\ComposerCreator;
use \Magento\Tools\Composer\Cleaner\ComposerCleaner;
use \Magento\Tools\Composer\Parser\AdminhtmlThemeXmlParser;
use \Magento\Tools\Composer\Parser\FrontendThemeXmlParser;

try {
    $opt = new \Zend_Console_Getopt(
        array(
            'verbose|v' => 'Detailed console logs',
            'clean|c' => 'Clean composer.json files from each component'
        )
    );
    $opt->parse();

    $logWriter = new \Zend_Log_Writer_Stream('php://output');
    $logWriter->setFormatter(new \Zend_Log_Formatter_Simple('[%timestamp%] : %message%' . PHP_EOL));
    $logger = new Zend_Log($logWriter);
    $logger->setTimestampFormat('H:i:s');
    $filter = $opt->getOption('v') ?
        new \Zend_Log_Filter_Priority(Zend_Log::DEBUG) :
        new \Zend_Log_Filter_Priority(Zend_Log::INFO);
    $logger->addFilter($filter);

    $clean = $opt->getOption('c')? true: false;

    $logger->info(sprintf('Your Magento Installation Directory: %s ', BP));

    $adminThemeExtractor = new AdminThemeExtractor(BP, $logger, new AdminhtmlThemeXmlParser());
    $frontEndThemeExtractor = new FrontendThemeExtractor(BP, $logger, new FrontendThemeXmlParser());

    $components = $adminThemeExtractor->extract(array(), $adminThemeCount);
    $logger->debug(sprintf("Read %3d admin themes.", $adminThemeCount));
    $components = $frontEndThemeExtractor->extract($components, $frontendThemeCount);
    $logger->debug(sprintf("Read %3d frontend themes.", $frontendThemeCount));

    $logger->info(sprintf("Starting to create composer.json for %3d components.", sizeof($components)));

    if ($clean) {
        $cleaner = new ComposerCleaner(BP, $logger);
        $cleanCount = $cleaner->clean($components);
        $logger->info(sprintf("SUCCESS: Cleaned %3d components.\n", $cleanCount));
        return;
    }

    $composerCreator = new ComposerCreator(BP, $components, $logger);
    $successCount = $composerCreator->create();

    $logger->info(sprintf("SUCCESS: Created %3d components. \n", $successCount));

} catch (\Zend_Console_Getopt_Exception $e) {
    exit($e->getUsageMessage());
}


