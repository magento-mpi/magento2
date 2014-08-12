<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
umask(0);

$appRoot = dirname(dirname(dirname(dirname(dirname(__DIR__)))));

require $appRoot . '/app/bootstrap.php';

$mtfRoot = dirname(dirname(dirname(__FILE__)));
$mtfRoot = str_replace('\\', '/', $mtfRoot);
define('MTF_BP', $mtfRoot);
define('MTF_TESTS_PATH', MTF_BP . '/tests/app/');

$path = get_include_path();
$path = rtrim($path, PATH_SEPARATOR);
$path .= PATH_SEPARATOR . MTF_BP;
$path .= PATH_SEPARATOR . MTF_BP . '/lib';
$path .= PATH_SEPARATOR . MTF_BP . '/tests/app';
$path .= PATH_SEPARATOR . MTF_BP . '/vendor/magento/mtf';
$path .= PATH_SEPARATOR . MTF_BP . '/vendor/phpunit/phpunit';
set_include_path($path);

/** @var \Magento\Framework\App\Bootstrap $bootstrap */
$bootstrap = require_once __DIR__ . '/../../../../../app/bootstrap.php';
/** @var \Mtf\Util\Generate\Factory $app */
$app = $bootstrap->createApplication('Mtf\Util\Generate\Factory');
$bootstrap->run($app);
\Mtf\Util\Generate\GenerateResult::displayResults();
