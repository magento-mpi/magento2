<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../../../app/bootstrap.php';
$includePath = new \Magento\Framework\Autoload\IncludePath();
spl_autoload_register([$includePath, 'load']);
$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);

$mtfRoot = dirname(dirname(dirname(__FILE__)));
$mtfRoot = str_replace('\\', '/', $mtfRoot);
define('MTF_BP', $mtfRoot);
define('MTF_TESTS_PATH', MTF_BP . '/tests/app/');

$paths = [
    MTF_BP,
    MTF_BP . '/lib',
    MTF_BP . '/tests/app',
    MTF_BP . '/generated',
    MTF_BP . '/vendor/magento/mtf',
    MTF_BP . '/vendor/phpunit/phpunit'
];
$includePath->addIncludePath($paths);

$om = $bootstrap->getObjectManager();
/** @var \Mtf\Util\Generate\Factory $generator */
$generator = $om->create('Mtf\Util\Generate\Factory');
$generator->launch();
\Mtf\Util\Generate\GenerateResult::displayResults();
