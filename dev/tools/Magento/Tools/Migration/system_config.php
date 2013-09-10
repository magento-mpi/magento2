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
\Magento\Autoload\IncludePath::addIncludePath(array($rootDir . '/lib', $rootDir . '/dev'));
$defaultReportFile = 'report.log';

try {
    $options = new Zend_Console_Getopt(array(
        'mode|w' => "Application mode.  Preview mode is default. If set to 'write' - file system is updated",
        'output|f-w' => "Report output type. Report is flushed to console by default."
            . "If set to 'file', report is written to file /log/report.log",
    ));

    $writerFactory = new \Magento\Tools\Migration\System\Writer\Factory();

    $fileManager = new \Magento\Tools\Migration\System\FileManager(new \Magento\Tools\Migration\System\FileReader(),
        $writerFactory->getWriter($options->getOption('mode'))
    );

    $loggerFactory = new \Magento\Tools\Migration\System\Configuration\Logger\Factory();
    $logger = $loggerFactory->getLogger($options->getOption('output'), $defaultReportFile, $fileManager);

    $generator = new \Magento\Tools\Migration\System\Configuration\Generator(
        new  \Magento\Tools\Migration\System\Configuration\Formatter(),
        $fileManager,
        $logger
    );

    $fieldMapper = new \Magento\Tools\Migration\System\Configuration\Mapper\Field();
    $groupMapper = new \Magento\Tools\Migration\System\Configuration\Mapper\Group($fieldMapper);
    $sectionMapper = new \Magento\Tools\Migration\System\Configuration\Mapper\Section($groupMapper);
    $tabMapper = new \Magento\Tools\Migration\System\Configuration\Mapper\Tab();
    $mapper = new \Magento\Tools\Migration\System\Configuration\Mapper($tabMapper, $sectionMapper);

    $parser = new \Magento\Tools\Migration\System\Configuration\Parser();
    $reader = new \Magento\Tools\Migration\System\Configuration\Reader($fileManager, $parser, $mapper);

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
