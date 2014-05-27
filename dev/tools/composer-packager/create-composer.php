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
$logger;
try {
    $opt = new Zend_Console_Getopt(
        array(
            'verbose|v' => 'Detailed console logs',
            'clean|c' => 'Clean composer.json files from each component',
        )
    );
    $opt->parse();


    $generationDir = $opt->getOption('o') ? $opt->getOption('o') : $generationDir;

    $logWriter = new \Magento\Composer\Log\Writer\DefaultWriter();
    $silentLogger = new \Magento\Composer\Log\Writer\QuietWriter();
    $logger = $opt->getOption('v') ? new \Magento\Composer\Log\Log($logWriter, $logWriter) : new \Magento\Composer\Log\Log( $logWriter, $silentLogger);

    $logger->debug("You selected %s. ", $generationDir);
    $logger->debug("Your root directory: %s ", $rootDir);

    $moduleExtractor= new \Magento\Composer\Extractor\ModuleExtractor($rootDir, $logger);
    $adminThemeExtractor = new \Magento\Composer\Extractor\AdminThemeExtractor($rootDir, $logger);
    $frontEndThemeExtractor = new \Magento\Composer\Extractor\FrontendThemeExtractor($rootDir, $logger);

    $modules = $moduleExtractor->extract();
    $logger->debug("Read %3d modules.", sizeof($modules));
    $adminThemes = $adminThemeExtractor->extract();
    $logger->debug("Read %3d admin themes.", sizeof($adminThemes));
    $frontendThemes = $frontEndThemeExtractor->extract();
    $logger->debug("Read %3d frontend themes.", sizeof($frontendThemes));

    $components = array_merge( $modules, $adminThemes, $frontendThemes);
    if($opt->getOption('c')){
        $cleaner = new \Magento\Composer\Helper\ComposerCleaner($components, $logger);
        $cleanCount = $cleaner->clean();
        $logger->log("SUCCESS: Cleaned %3d components.\n", $cleanCount);
        return;
    }
    // $logger->log("SUCCESS: Created %3d components.\n", sizeof($components));

    $composerCreator = new \Magento\Composer\Creator\ComposerCreator($components, $logger);
    $successCount = $composerCreator->create();
    $logger->log("SUCCESS: Created %3d components. \n", $successCount);

} catch (Zend_Console_Getopt_Exception $e) {
    exit($e->getUsageMessage());
}


