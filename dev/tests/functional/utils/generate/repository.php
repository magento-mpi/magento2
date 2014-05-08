<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
umask(0);

$mtfRoot = dirname(dirname(dirname(__FILE__)));
$mtfRoot = str_replace('\\', '/', $mtfRoot);
define('MTF_BP', $mtfRoot);
define('MTF_TESTS_PATH', MTF_BP . '/tests/app/');

$path = get_include_path();
$path = rtrim($path, PATH_SEPARATOR);
$path .= PATH_SEPARATOR . MTF_BP . '/lib';
$path .= PATH_SEPARATOR . MTF_BP . '/vendor/magento/mtf';
$path .= PATH_SEPARATOR . MTF_BP . '/vendor/magento/mtf/lib';
set_include_path($path);

$appRoot = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
require $appRoot . '/app/bootstrap.php';

$objectManagerFactory = new \Magento\Framework\App\ObjectManagerFactory();

$arguments = $_SERVER;
$objectManager = $objectManagerFactory->create(BP, $arguments);
\Mtf\ObjectManagerFactory::configure($objectManager);
$objectManager->get('Magento\Framework\App\State')->setAreaCode('frontend');
$objectManager->create('Mtf\Util\Generate\Repository')->launch();
\Mtf\Util\Generate\GenerateResult::displayResults();