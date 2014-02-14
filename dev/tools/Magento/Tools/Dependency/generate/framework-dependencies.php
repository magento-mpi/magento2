<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

require_once __DIR__ . '/bootstrap.php';

use Magento\TestFramework\Utility\Files;
use Magento\Tools\Dependency\ServiceLocator;

try {
    $filesUtility = Files::init();
    $filesForParse = $filesUtility->getFiles(array($filesUtility->getPathToSource() . '/app/code/Magento'), '*');
    $configFiles = $filesUtility->getConfigFiles('module.xml', [], false);

    ServiceLocator::getFrameworkDependenciesReportBuilder()->build([
        'files_for_parse' => $filesForParse,
        'config_files' => $configFiles,
        'report_filename' => 'framework-dependencies.csv',
    ]);

    fwrite(STDOUT, PHP_EOL . 'Report successfully processed.' . PHP_EOL);

} catch (\Zend_Console_Getopt_Exception $e) {
    fwrite(STDERR, $e->getUsageMessage() . PHP_EOL);
    exit(1);
} catch (\Exception $e) {
    fwrite(STDERR, 'Dependencies report generator failed: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
