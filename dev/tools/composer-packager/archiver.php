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
            'output|o=s' => 'Generation dir. Default value ' . $generationDir,
        )
    );
    $opt->parse();

    $generationDir = $opt->getOption('o') ? $opt->getOption('o') : $generationDir;
    $logWriter = new \Zend_Log_Writer_Stream('php://output');

    $logWriter->setFormatter(new \Zend_Log_Formatter_Simple('[%timestamp%] : %message%' . PHP_EOL));
    $logger = new Zend_Log($logWriter);
    $logger->setTimestampFormat("H:i:s");
    $filter = $opt->getOption('v') ? new \Zend_Log_Filter_Priority(Zend_Log::DEBUG) : new \Zend_Log_Filter_Priority(Zend_Log::INFO);
    $logger->addFilter($filter);

    $logger->info(sprintf("Your archives output directory: %s. ", $generationDir));
    $logger->info(sprintf("Your Magento Installation Directory: %s ", $rootDir));

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

    try {
        if (!file_exists($generationDir)) {
            mkdir($generationDir, 0777, true);
        }
    } catch(\Exception $ex){
        $logger->error(sprintf("ERROR: Creating Directory %s failed. Message: %s", $generationDir, $ex->getMessage()));
        exit($e->getCode());
    }

    $logger->debug(sprintf("Zip Archive Location: %s", $generationDir));
    $noOfZips = 0;
    foreach($components as $component) {
        $name = \Magento\Composer\Helper\Converter::vendorPackagetoName($component->getName());
        $noOfZips += \Magento\Composer\Helper\Zip::Zip($component->getLocation(), $generationDir . "/" . $name . "-". $component->getVersion() . ".zip");
        $logger->debug(sprintf("Created zip archive for %-40s [%9s]", $component->getName(), $component->getVersion()));
    }
    $logger->info(sprintf("SUCCESS: Zipped ". $noOfZips." packages. You should be able to find it at %s. \n", $generationDir));

} catch (\Zend_Console_Getopt_Exception $e) {
    exit($e->getUsageMessage());
}

