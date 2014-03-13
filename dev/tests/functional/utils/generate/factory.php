<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
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
set_include_path($path);

$entryPoint = new \Magento\App\EntryPoint\EntryPoint(BP, $_SERVER);
$entryPoint->run('Mtf\Util\Generate\Factory');
\Mtf\Util\Generate\GenerateResult::displayResults();
