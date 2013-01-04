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

try {
    $params = getopt('',array('deploy-dir:', 'deploy-url-pattern:', 'dsn:', 'install::', 'uninstall::', 'wipe::'));
    $workingDir = realpath(__DIR__ . '/../../..');
    $controller = new \Magento\MultiTenant\Wizard($params, $workingDir, dirname($params['deploy-dir']));
    $controller->execute();
} catch (Exception $e) {
    echo 'USAGE:
    php -f deploy.php --
        --deploy-dir=<deployment_dir> --deploy-url-pattern=<url_pattern>
        --dsn=mysql://<db_user>:<db_password>@<db_host>
        [--install=<tenant_ids>] [--uninstall=<tenant_ids>]
        [--wipe]' . PHP_EOL . PHP_EOL;
    echo $e;
    exit(1);
}
