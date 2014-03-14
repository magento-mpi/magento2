<?php
/**
 * Application entry point
 *
 * Example - run a particular store or website:
 * --------------------------------------------
 * $params = $_SERVER;
 * $params['MAGE_RUN_CODE'] = 'website2';
 * $params['MAGE_RUN_TYPE'] = 'website';
 * ...
 * $entryPoint = new \Magento\App\EntryPoint\EntryPoint(BP, $params);
 * --------------------------------------------
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
require __DIR__ . '/app/bootstrap.php';
$_SERVER['MAGE_MODE'] = 'developer';
ini_set('display_errors', 1);
$entryPoint = new \Magento\App\EntryPoint\EntryPoint(BP, $_SERVER);
$entryPoint->run('Magento\App\Http');
