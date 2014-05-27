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
            'output|o=s' => 'Generation dir. Default value ' . $generationDir,
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
    $logger->debug("Completed creating %3d modules.", sizeof($modules));
    $adminThemes = $adminThemeExtractor->extract();
    $logger->debug("Completed creating %3d admin themes.", sizeof($adminThemes));
    $frontendThemes = $frontEndThemeExtractor->extract();
    $logger->debug("Completed creating %3d frontend themes.", sizeof($frontendThemes));

    $components = array_merge( $modules, $adminThemes, $frontendThemes);

    try {
        if (!file_exists($generationDir)) {
            mkdir($generationDir, 0777, true);
        }
    } catch(\Exception $ex){
        $logger->error("ERROR: Creating Directory %s failed. Message: %s", $generationDir, $ex->getMessage());
        exit($e->getCode());
    }
    $logger->debug("Zip Archive Location: %s", $generationDir);
    foreach($components as $component) {
        $name = \Magento\Composer\Helper\Converter::vendorPackagetoName($component->getName());
        \Magento\Composer\Helper\Zip::Zip(realpath($component->getLocation()), $generationDir . "/" . $name . "-". $component->getVersion() . ".zip");
        $logger->debug("Created zip archive for %-40s [%9s]", $component->getName(), $component->getVersion());
    }
    $logger->log("SUCCESS: Zipped all packages. You should be able to find it at %s. \n", $generationDir);

} catch (Zend_Console_Getopt_Exception $e) {
    exit($e->getUsageMessage());
}

