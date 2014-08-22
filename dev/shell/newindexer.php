<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

use Magento\Framework\App\Bootstrap;
use Magento\Store\Model\StoreManager;

require __DIR__ . '/../../app/bootstrap.php';
$params = $_SERVER;
$params[StoreManager::PARAM_RUN_CODE] = 'admin';
$params[StoreManager::PARAM_RUN_TYPE] = 'store';
$bootstrap = new Bootstrap(BP, $params);
/** @var Magento\Indexer\App\Shell $app */
$app = $bootstrap->createApplication('Magento\Indexer\App\Shell', ['entryFileName' => basename(__FILE__)]);
$bootstrap->run($app);
