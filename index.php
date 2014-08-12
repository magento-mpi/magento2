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
 * $entryPoint = new \Magento\Framework\App\EntryPoint\EntryPoint(BP, $params);
 * --------------------------------------------
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/** @var \Magento\Framework\App\Bootstrap $bootstrap */
$bootstrap = require __DIR__ . '/app/bootstrap.php';
/** @var \Magento\Framework\App\Http $app */
$app = $bootstrap->createApplication('Magento\Framework\App\Http');
$bootstrap->run($app);
