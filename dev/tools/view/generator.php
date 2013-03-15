<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     tools
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Script to pre-deploy Magento - copy all the static view files to public directory,
 * so in production mode the paths and urls can be composed without looking for files on disk.
 */

require __DIR__ . '/../../../app/bootstrap.php';
require __DIR__ . '/Generator/ThemeProxy.php';
require __DIR__ . '/Generator/Command.php';

// ----Parse params and run the tool-------------------------
define('USAGE', <<<USAGE
$>./generator.php -- [--destination=<path>] [--dry-run] [-h] [--help]
    Pre-deploy Magento view files to a public directory.
    Additional parameters:
    --destination=<path>    custom path to copy files to, if not specified, then default one within system is used
    --dry-run               run through files, but do not copy anything
    -h or --help            print usage
USAGE
);

$options = getopt('h', array('help', 'dry-run', 'destination:'));
if (isset($options['h']) || isset($options['help'])) {
    echo USAGE;
    exit(0);
}

$logWriter = new Zend_Log_Writer_Stream('php://output');
$logWriter->setFormatter(new Zend_Log_Formatter_Simple('%message%' . PHP_EOL));
$logger = new Zend_Log($logWriter);

try {
    $generator = new Generator_CopyRule($logger, $options);
    $generator->getCopyRules();
} catch (Exception $e) {
    $logger->log('Error: ' . $e->getMessage(), Zend_Log::ERR);
    exit(1);
}
