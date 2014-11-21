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
    $console = new \Zend_Console_Getopt(array('directory|d=s' => 'Path to base directory for parsing'));
    $console->parse();

    $directory = $console->getOption('directory') ?: BP;

    Files::setInstance(new \Magento\TestFramework\Utility\Files($directory));
    $filesForParse = Files::init()->getComposerFiles('code/Magento', false);

    ServiceLocator::getCircularDependenciesReportBuilder()->build(
        array(
            'parse' => array('files_for_parse' => $filesForParse),
            'write' => array('report_filename' => 'modules-circular-dependencies.csv')
        )
    );

    fwrite(STDOUT, PHP_EOL . 'Report successfully processed.' . PHP_EOL);
} catch (\Zend_Console_Getopt_Exception $e) {
    fwrite(STDERR, $e->getUsageMessage() . PHP_EOL);
    exit(1);
} catch (\Exception $e) {
    fwrite(STDERR, 'Please, check passed path. Dependencies report generator failed: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
