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

    $logger->info(sprintf("Your selected Generation Directory: %s. ", $generationDir));
    $logger->info(sprintf("Your Magento Installation Directory: %s ", $rootDir));

    $moduleExtractor= new \Magento\Composer\Extractor\ModuleExtractor($rootDir, $logger);
    $adminThemeExtractor = new \Magento\Composer\Extractor\AdminThemeExtractor($rootDir, $logger);
    $frontEndThemeExtractor = new \Magento\Composer\Extractor\FrontendThemeExtractor($rootDir, $logger);

    $modules = $moduleExtractor->extract();
    $logger->debug(sprintf("Completed creating %3d modules.", sizeof($modules)));
    $adminThemes = $adminThemeExtractor->extract();
    $logger->debug(sprintf("Completed creating %3d admin themes.", sizeof($adminThemes)));
    $frontendThemes = $frontEndThemeExtractor->extract();
    $logger->debug(sprintf("Completed creating %3d frontend themes.", sizeof($frontendThemes)));

    $components = array_merge( $modules, $adminThemes, $frontendThemes);

    try {
        if (!file_exists($generationDir)) {
            mkdir($generationDir, 0777, true);
        }
    } catch(\Exception $ex){
        $logger->error(sprintf("ERROR: Creating Directory %s failed. Message: %s", $generationDir, $ex->getMessage()));
        exit($e->getCode());
    }
    $logger->debug(sprintf("Zip Archive Location: %s", $generationDir));
    foreach($components as $component) {
        $name = \Magento\Composer\Helper\Converter::vendorPackagetoName($component->getName());
        \Magento\Composer\Helper\Zip::Zip(realpath($component->getLocation()), $generationDir . "/" . $name . "-". $component->getVersion() . ".zip");
        $logger->debug(sprintf("Created zip archive for %-40s [%9s]", $component->getName(), $component->getVersion()));
    }
    $logger->info(sprintf("SUCCESS: Zipped all packages. You should be able to find it at %s. \n", $generationDir));

} catch (\Zend_Console_Getopt_Exception $e) {
    exit($e->getUsageMessage());
}

