<?php
/**
 * Application entry point
 *
 * Example - run a particular store or website:
 * --------------------------------------------
 * require __DIR__ . '/app/bootstrap.php';
 * $params = $_SERVER;
 * $params[\Magento\Store\Model\StoreManager::PARAM_RUN_CODE] = 'website2';
 * $params[\Magento\Store\Model\StoreManager::PARAM_RUN_TYPE] = 'website';
 * $bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $params);
 * \/** @var \Magento\Framework\App\Http $app *\/
 * $app = $bootstrap->createApplication('Magento\Framework\App\Http');
 * $bootstrap->run($app);
 * --------------------------------------------
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

require __DIR__ . '/app/bootstrap.php';
$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);
/** @var \Magento\Framework\App\Http $app */
$app = $bootstrap->createApplication('Magento\Framework\App\Http');
$bootstrap->run($app);
