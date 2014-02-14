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

    ServiceLocator::getDependenciesReportBuilder()->build([
        'report_filename' => 'modules-dependencies.csv',
        'files_for_parse' => Files::init()->getConfigFiles('module.xml', [], false),
    ]);

    fwrite(STDOUT, PHP_EOL . 'Report successfully processed.' . PHP_EOL);

} catch (\Zend_Console_Getopt_Exception $e) {
    fwrite(STDERR, $e->getUsageMessage() . PHP_EOL);
    exit(1);
} catch (\Exception $e) {
    fwrite(STDERR, 'Dependencies report generator failed: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
