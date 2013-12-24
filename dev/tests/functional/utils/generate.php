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

$appRoot = dirname(dirname(dirname(dirname(__DIR__))));
$mtfRoot = dirname(dirname(__FILE__));
require_once $mtfRoot . '/bootstrap.php';

$generatorConfigFile = $mtfRoot . "/utils/config/generator_config.yml.dist";
$generatorConfig = new \Mtf\System\Config($generatorConfigFile);
$params = $generatorConfig->getConfigParam();

$params['mtf_app_root'] = $appRoot;
$params['mtf_mtf_root'] = $mtfRoot;

$params = array_merge($params, $_REQUEST);

$entryPoint = new \Mtf\Util\EntryPoint($params);
$entryPoint->processRequest();
