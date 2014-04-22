<?php
/**
 * A command line tool that pre-populates static view files into public directory.
 * In the production mode paths and URLs are to be composed without the filesystem lookup.
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Tools
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../../../app/bootstrap.php';
\Magento\Autoload\IncludePath::addIncludePath(__DIR__ . '/../../../');

/**
 * Command line usage help
 */
define(
    'SYNOPSIS',
<<<USAGE
Usage: php -f generator.php -- [--source <dir>] [--destination <dir>] [--dry-run]
       php -f generator.php -- --help

  --source <dir>      Root directory to start search of static view files from.
                      If omitted, the application root directory is used.

  --destination <dir> Directory to copy files to.
                      If omitted, public location of static view files is used.

  --dry-run           Do not create directories and files in a destination path.

  --help              Print this usage information.

USAGE
);

$logWriter = new \Zend_Log_Writer_Stream('php://output');
$logWriter->setFormatter(new \Zend_Log_Formatter_Simple('%message%' . PHP_EOL));
$logger = new \Zend_Log($logWriter);

$options = getopt('', array('help', 'dry-run', 'source:', 'destination:'));
if (isset($options['help'])) {
    $logger->log(SYNOPSIS, \Zend_Log::INFO);
    exit(0);
}

$logger->log('Deploying...', \Zend_Log::INFO);
try {
    $objectManagerFactory = new \Magento\Framework\App\ObjectManagerFactory();
    $objectManager = $objectManagerFactory->create(
        BP,
        array(\Magento\Framework\App\State::PARAM_MODE => \Magento\Framework\App\State::MODE_PRODUCTION)
    );

    /** @var \Magento\Tools\View\Generator\Config $config */
    $config = $objectManager->create('Magento\Tools\View\Generator\Config', array(
        'cmdOptions' => $options,
        'allowedFiles' => array('.htaccess'),
    ));

    // Register the deployment directory
    /** @var \Magento\Framework\Filesystem\DirectoryList $directoryList */
    $directoryList = $objectManager->get('Magento\Framework\Filesystem\DirectoryList');
    $directoryList->addDirectory('deployment', array('path' => $config->getDestinationDir()));

    /** @var \Magento\Core\Model\Theme\Collection $themes */
    $themes = $objectManager->create('Magento\Core\Model\Theme\Collection');
    $themes->setItemObjectClass('Magento\Tools\View\Generator\ThemeLight');
    $themes->addDefaultPattern('*');

    /** @var \Magento\Framework\View\Design\Fallback\RulePool $fallbackPool */
    $fallbackPool = $objectManager->create('Magento\View\Design\Fallback\RulePool');

    /** @var \Magento\Tools\View\Generator\CopyRule $generator */
    $generator = $objectManager->create('Magento\Tools\View\Generator\CopyRule', array(
        'themes' => $themes,
        'fallbackRule' => $fallbackPool->getRule(\Magento\Framework\View\Design\Fallback\RulePool::TYPE_STATIC_FILE)
    ));

    $copyRules = $generator->getCopyRules();

    /** @var \Magento\Framework\App\View\Deployment\Version\Storage\File $versionFile */
    $versionFile = $objectManager->create('Magento\Framework\App\View\Deployment\Version\Storage\File', array(
        'directoryCode' => 'deployment',
    ));

    /** @var \Magento\Tools\View\Generator\ThemeDeployment $deployment */
    $deployment = $objectManager->create('Magento\Tools\View\Generator\ThemeDeployment', array(
        'destinationHomeDir' => $config->getDestinationDir(),
        'configPermitted' => __DIR__ . '/config/permitted.php',
        'configForbidden' => __DIR__ . '/config/forbidden.php',
        'versionStorage' => $versionFile,
        'isDryRun' => $config->isDryRun(),
        'preProcessor' => $objectManager->create('Magento\Framework\View\Asset\PreProcessor\Composite')
    ));
    $deployment->run($copyRules);
} catch (\Exception $e) {
    $logger->log('Error: ' . $e->getMessage(), \Zend_Log::ERR);
    exit(1);
}
$logger->log('Completed successfully.', \Zend_Log::INFO);
