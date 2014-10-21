<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

$autoload = __DIR__ . '/vendor/autoload.php';

if (!file_exists($autoload)) {
    if (PHP_SAPI == 'cli') {
        echo "Dependencies not installed. Please run 'composer install' under /setup directory.\n";
    } else {
        echo <<<HTML
<div style="font:12px/1.35em arial, helvetica, sans-serif;">
    <div style="margin:0 0 25px 0; border-bottom:1px solid #ccc;">
        <h3 style="margin:0;font-size:1.7em;font-weight:normal;text-transform:none;text-align:left;color:#2f2f2f;">
        Whoops, it looks like setup tool dependencies are not installed.</h3>
    </div>
    <p>Please run 'composer install' under /setup directory.</p>
</div>
HTML;
    }
    exit(1);
}

require $autoload;

$application = \Zend\Mvc\Application::init(require 'config/application.config.php');
$magentoBootstrap = new \Magento\Setup\Bootstrap($_SERVER);
$magentoBootstrap->initialize($application);
$application->run();
