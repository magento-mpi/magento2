<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

require __DIR__ . '/../../../app/bootstrap.php';
$rootDir = realpath(__DIR__ . '/../../../');
$generationDir = __DIR__ . '/packages';
try {
    $opt = new \Zend_Console_Getopt(
        array(
            'verbose|v' => 'Detailed console logs',
            'clean|c' => 'Clean composer.json files from each component',
        )
    );
    $opt->parse();


    $generationDir = $opt->getOption('o') ? $opt->getOption('o') : $generationDir;
    $logWriter = new \Zend_Log_Writer_Stream('php://output');

    $logWriter->setFormatter(new \Zend_Log_Formatter_Simple('%message%' . PHP_EOL));
    $logger = new Zend_Log($logWriter);

    $filter = $opt->getOption('v') ? new \Zend_Log_Filter_Priority(Zend_Log::DEBUG) : new \Zend_Log_Filter_Priority(Zend_Log::INFO);
    $logger->addFilter($filter);

    $logger->info(sprintf("You selected %s. ", $generationDir));
    $logger->info(sprintf("Your root directory: %s ", $rootDir));


    $moduleExtractor= new \Magento\Composer\Extractor\ModuleExtractor($rootDir, $logger);
    $adminThemeExtractor = new \Magento\Composer\Extractor\AdminThemeExtractor($rootDir, $logger);
    $frontEndThemeExtractor = new \Magento\Composer\Extractor\FrontendThemeExtractor($rootDir, $logger);
    $libraryExtractor = new \Magento\Composer\Extractor\LibraryExtractor($rootDir, $logger);
    $frameworkExtractor = new \Magento\Composer\Extractor\FrameworkExtractor($rootDir, $logger);
    $languagePackExtractor = new \Magento\Composer\Extractor\LanguagePackExtractor($rootDir, $logger);

    $components = $moduleExtractor->extract(null, $moduleCount);
    $logger->debug(sprintf("Read %3d modules.", $moduleCount));
    $components = $adminThemeExtractor->extract($components, $adminThemeCount);
    $logger->debug(sprintf("Read %3d admin themes.", $adminThemeCount));
    $components = $frontEndThemeExtractor->extract($components, $frontendThemeCount);
    $logger->debug(sprintf("Read %3d frontend themes.", $frontendThemeCount));
    $components = $libraryExtractor->extract($components , $libraryCount);
    $logger->debug(sprintf("Read %3d libraries.", $libraryCount));
    $components = $frameworkExtractor->extract($components, $frameworkCount);
    $logger->debug(sprintf("Read %3d frameworks.", $frameworkCount));
    $components = $languagePackExtractor->extract($components, $languagePackCount);
    $logger->debug(sprintf("Read %3d language packs.", $languagePackCount));

    $logger->info(sprintf("Starting to create composer.json for %3d components.", sizeof($components)));

    if($opt->getOption('c')){
        $cleaner = new \Magento\Composer\Helper\ComposerCleaner($components, $logger);
        $cleanCount = $cleaner->clean();
        $logger->info(sprintf("SUCCESS: Cleaned %3d components.\n", $cleanCount));
        return;
    }

    $composerCreator = new \Magento\Composer\Creator\ComposerCreator($components, $logger);
    $successCount = $composerCreator->create();
    $logger->info(sprintf("SUCCESS: Created %3d components. \n", $successCount));

} catch (\Zend_Console_Getopt_Exception $e) {
    exit($e->getUsageMessage());
}


