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

use \Magento\Tools\Composer\Extractor\ModuleExtractor;
use \Magento\Tools\Composer\Extractor\EnterpriseExtractor;
use \Magento\Tools\Composer\Extractor\CommunityExtractor;
use \Magento\Tools\Composer\Extractor\AdminThemeExtractor;
use \Magento\Tools\Composer\Extractor\FrameworkExtractor;
use \Magento\Tools\Composer\Extractor\FrontendThemeExtractor;
use \Magento\Tools\Composer\Extractor\LanguagePackExtractor;
use \Magento\Tools\Composer\Extractor\LibraryExtractor;
use \Magento\Tools\Composer\Creator\ComposerCreator;
use \Magento\Tools\Composer\Cleaner\ComposerCleaner;
use \Magento\Tools\Composer\Parser\ModuleXmlParser;
use \Magento\Tools\Composer\Parser\LanguagePackXmlParser;
use \Magento\Tools\Composer\Parser\LibraryXmlParser;
use \Magento\Tools\Composer\Parser\ThemeXmlParser;
use \Magento\Tools\Composer\Parser\NullParser;

try {
    $opt = new \Zend_Console_Getopt(
        array(
            'edition|e=s' => 'Edition of which packaging is done. Acceptable values: [ee|enterprise] or [ce|community]',
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
    $edition = $opt->getOption('e') ?: null;
    if (!$edition) {
        $logger->info('Edition is required. Acceptable values [ee|enterprise] or [ce|community]');
        exit;
    }

    switch (strtolower($edition)) {
        case 'enterprise':
        case 'ee':
            $logger->info('Your Edition: Enterprise');
            $productExtractor = new EnterpriseExtractor(BP, $logger, new NullParser());
            break;
        case 'community':
        case 'ce':
            $logger->info('Your Edition: Community');
            $productExtractor = new CommunityExtractor(BP, $logger, new NullParser());
            break;
        default:
            $logger->info('Edition value not acceptable. Acceptable values: [ee|ce]');
            exit;
    }


    $logger->info(sprintf('Your Magento Installation Directory: %s ', BP));

    $moduleExtractor= new ModuleExtractor(BP, $logger, new ModuleXmlParser());
    $adminThemeExtractor = new AdminThemeExtractor(BP, $logger, new ThemeXmlParser());
    $frontEndThemeExtractor = new FrontendThemeExtractor(BP, $logger, new ThemeXmlParser());
    $libraryExtractor = new LibraryExtractor(BP, $logger, new LibraryXmlParser());
    $frameworkExtractor = new FrameworkExtractor(BP, $logger, new LibraryXmlParser());
    $languagePackExtractor = new LanguagePackExtractor(BP, $logger, new LanguagePackXmlParser());

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

    $logger->info(sprintf("Starting to create composer.json for %3d components.", sizeof($components)));

    if ($clean) {
        $cleaner = new ComposerCleaner(BP, $components, $logger);
        $cleanCount = $cleaner->clean();
        $logger->info(sprintf("SUCCESS: Cleaned %3d components.\n", $cleanCount));
        return;
    }

    $composerCreator = new ComposerCreator(BP, $components, $logger);
    $successCount = $composerCreator->create();

    $logger->info(sprintf("SUCCESS: Created %3d components. \n", $successCount));

} catch (\Zend_Console_Getopt_Exception $e) {
    exit($e->getUsageMessage());
}


