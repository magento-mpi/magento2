<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

use Magento\Store\Model\StoreManager;

/** @var \Magento\Framework\App\Bootstrap $bootstrap */
$bootstrap = require __DIR__ . '/../../app/bootstrap.php';
$bootstrap->addParams([StoreManager::PARAM_RUN_CODE => 'admin', StoreManager::PARAM_RUN_TYPE => 'store']);
/** @var Magento\Indexer\App\Shell $app */
$app = $bootstrap->createApplication('Magento\Indexer\App\Shell', ['entryFileName' => basename(__FILE__)]);
$bootstrap->run($app);
