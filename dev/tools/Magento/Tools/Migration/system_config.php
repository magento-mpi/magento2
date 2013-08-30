<?php
/**
 * {license_notice}
 *
 * @category   Tools
 * @package    system_configuration
 * @copyright  {copyright}
 * @license    {license_link}
 */

$rootDir = realpath(__DIR__ . '../../../..');
require __DIR__ . '/../../../app/autoload.php';
Magento_Autoload_IncludePath::addIncludePath(array($rootDir . '/lib', $rootDir . '/dev'));
$defaultReportFile = 'report.log';

try {
    $options = new Zend_Console_Getopt(array(
        'mode|w' => "Application mode.  Preview mode is default. If set to 'write' - file system is updated",
        'output|f-w' => "Report output type. Report is flushed to console by default."
            . "If set to 'file', report is written to file /log/report.log",
    ));

    $writerFactory = new Magento_Tools_Migration_System_Writer_Factory();

    $fileManager = new Magento_Tools_Migration_System_FileManager(new Magento_Tools_Migration_System_FileReader(),
        $writerFactory->getWriter($options->getOption('mode'))
    );

    $loggerFactory = new Magento_Tools_Migration_System_Configuration_Logger_Factory();
    $logger = $loggerFactory->getLogger($options->getOption('output'), $defaultReportFile, $fileManager);

    $generator = new Magento_Tools_Migration_System_Configuration_Generator(
        new  Magento_Tools_Migration_System_Configuration_Formatter(),
        $fileManager,
        $logger
    );

    $fieldMapper = new Magento_Tools_Migration_System_Configuration_Mapper_Field();
    $groupMapper = new Magento_Tools_Migration_System_Configuration_Mapper_Group($fieldMapper);
    $sectionMapper = new Magento_Tools_Migration_System_Configuration_Mapper_Section($groupMapper);
    $tabMapper = new Magento_Tools_Migration_System_Configuration_Mapper_Tab();
    $mapper = new Magento_Tools_Migration_System_Configuration_Mapper($tabMapper, $sectionMapper);

    $parser = new Magento_Tools_Migration_System_Configuration_Parser();
    $reader = new Magento_Tools_Migration_System_Configuration_Reader($fileManager, $parser, $mapper);

    foreach ($reader->getConfiguration() as $file => $config) {
        $generator->createConfiguration($file, $config);
        $fileManager->remove($file);
    }
    $logger->report();
} catch (Zend_Console_Getopt_Exception $e) {
    echo $e->getUsageMessage();
    exit;
} catch (InvalidArgumentException $exp) {
    echo $exp->getMessage();
} catch (Exception $exp) {
    echo $exp->getMessage();
}
