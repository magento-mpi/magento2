<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

ini_set('memory_limit', '-1');

require __DIR__ . '/../../../bootstrap.php';
$generationDir = __DIR__ . '/_packages';

use Magento\Tools\Composer\Extractor\ModuleExtractor;
use \Magento\Tools\Composer\Extractor\EnterpriseExtractor;
use \Magento\Tools\Composer\Extractor\CommunityExtractor;
use \Magento\Tools\Composer\Extractor\AdminThemeExtractor;
use \Magento\Tools\Composer\Extractor\FrameworkExtractor;
use \Magento\Tools\Composer\Extractor\FrontendThemeExtractor;
use \Magento\Tools\Composer\Extractor\LanguagePackExtractor;
use \Magento\Tools\Composer\Extractor\LibraryExtractor;
use \Magento\Tools\Composer\Helper\Zip;
use \Magento\Tools\Composer\Helper\Converter;

/**
 * Composer Archiver Tool
 *
 * This tool creates archive (zip) packages for each component in Magento, as well as the skeleton package.
 */
try {
    $opt = new \Zend_Console_Getopt(
        array(
            'edition|e=s' => 'Edition of which packaging is done. Acceptable values: [ee|enterprise] or [ce|community]',
            'verbose|v' => 'Detailed console logs',
            'output|o=s' => 'Generation dir. Default value ' . $generationDir,
        )
    );
    $opt->parse();

    $generationDir = $opt->getOption('o') ?: $generationDir;
    $logWriter = new \Zend_Log_Writer_Stream('php://output');

    $edition = $opt->getOption('e') ?: null;
    if (!$edition) {
        throw new \InvalidArgumentException('Edition is required. Acceptable values [ee|enterprise] or [ce|community]');
    }

    $logWriter->setFormatter(new \Zend_Log_Formatter_Simple('[%timestamp%] : %message%' . PHP_EOL));
    $logger = new Zend_Log($logWriter);
    $logger->setTimestampFormat("H:i:s");
    $filter = $opt->getOption('v') ?
            new \Zend_Log_Filter_Priority(Zend_Log::DEBUG) :
            new \Zend_Log_Filter_Priority(Zend_Log::INFO);
    $logger->addFilter($filter);

    $logger->info(sprintf("Your archives output directory: %s. ", $generationDir));
    $logger->info(sprintf("Your Magento Installation Directory: %s ", BP));

    switch (strtolower($edition)) {
        case 'enterprise':
        case 'ee':
            $logger->info('Your Edition: Enterprise');
            $productExtractor = new EnterpriseExtractor(BP, $logger);
            break;
        case 'community':
        case 'ce':
            $logger->info('Your Edition: Community');
            $productExtractor = new CommunityExtractor(BP, $logger);
            break;
        default:
            throw new \InvalidArgumentException('Edition value not acceptable. Acceptable values: [ee|ce]');
    }

    $moduleExtractor= new ModuleExtractor(BP, $logger);
    $adminThemeExtractor = new AdminThemeExtractor(BP, $logger);
    $frontEndThemeExtractor = new FrontendThemeExtractor(BP, $logger);
    $libraryExtractor = new LibraryExtractor(BP, $logger);
    $frameworkExtractor = new FrameworkExtractor(BP, $logger);
    $languagePackExtractor = new LanguagePackExtractor(BP, $logger);

    $components = $moduleExtractor->extract(array(), $moduleCount);
    $logger->debug(sprintf("Read %3d modules.", $moduleCount));
    $components = $adminThemeExtractor->extract($components, $adminThemeCount);
    $logger->debug(sprintf("Read %3d admin themes.", $adminThemeCount));
    $components = $frontEndThemeExtractor->extract($components, $frontendThemeCount);
    $logger->debug(sprintf("Read %3d frontend themes.", $frontendThemeCount));
    $components = $libraryExtractor->extract($components, $libraryCount);
    $logger->debug(sprintf("Read %3d libraries.", $libraryCount));
    $components = $frameworkExtractor->extract($components, $frameworkCount);
    $logger->debug(sprintf("Read %3d frameworks.", $frameworkCount));
    $components = $languagePackExtractor->extract($components, $languagePackCount);
    $logger->debug(sprintf("Read %3d language packs.", $languagePackCount));
    $components = $productExtractor->extract($components, $productCount);
    $logger->debug(sprintf('Created %s edition project', $edition));

    try {
        if (!file_exists($generationDir)) {
            mkdir($generationDir, 0777, true);
        }
    } catch (\Exception $ex) {
        $logger->error(sprintf("ERROR: Creating Directory %s failed. Message: %s", $generationDir, $ex->getMessage()));
        exit($e->getCode());
    }

    $logger->debug(sprintf("Zip Archive Location: %s", $generationDir));
    $noOfZips = 0;
    foreach ($components as $component) {
        $excludes = array();
        if ($component->getType() == "project") {
            $excludes = array(
                realpath(BP) . "/app/design/adminhtml/Magento",
                realpath(BP) . "/app/design/frontend/Magento",
                realpath(BP) . "/app/code/Magento",
                realpath(BP) . "/dev/tools/Magento/Tools/Composer/_packages"
            );
        }
        $name = Converter::vendorPackagetoName($component->getName());
        $noOfZips += Zip::zip(
            $component->getLocation(),
            $generationDir . "/" . $name . "-". $component->getVersion() . ".zip", $excludes
        );
        $logger->debug(sprintf("Created zip archive for %-40s [%9s]", $component->getName(), $component->getVersion()));
    }
    $logger->info(
        sprintf("SUCCESS: Zipped ". $noOfZips." packages. You should be able to find it at %s. \n", $generationDir)
    );

} catch (\Zend_Console_Getopt_Exception $e) {
    exit($e->getUsageMessage());
}
