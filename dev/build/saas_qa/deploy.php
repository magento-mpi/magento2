<?php
/**
 * Multi-tenant build deployment script
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../app/autoload.php';
Magento_Autoload_IncludePath::addIncludePath(array(__DIR__ . '/../../../lib', __DIR__));

$logWriter = new \Zend_Log_Writer_Stream('php://output');
$logWriter->setFormatter(new \Zend_Log_Formatter_Simple('%message%' . PHP_EOL));
$logger = new \Zend_Log($logWriter);
try {
    $params = getopt('', array('url-template:', 'dsn:', 'install::', 'uninstall::', 'cleanup::'));
    $workingDir = realpath(__DIR__ . '/../../..');
    $metaDir = __DIR__ . '/tenants';
    if (!is_dir($metaDir)) {
        mkdir($metaDir);
    }
    $controller = new \Magento\MultiTenant\Wizard($logger, $params, $workingDir, $metaDir);
    $controller->execute();
} catch (Exception $e) {
    $logger->log('USAGE:
    php -f deploy.php -- --url-template=http://*.tenant.<domain>/ --dsn=mysql://<db_user>:<db_password>@<db_host>
        [--install=<tenant_ids>] [--uninstall=<tenant_ids>]
        [--cleanup]' . PHP_EOL . PHP_EOL, Zend_Log::INFO);
    $logger->log((string)$e, Zend_Log::ERR);
    exit(1);
}
