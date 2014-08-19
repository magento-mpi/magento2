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
$bootstrap = new Bootstrap(
    BP,
    $_SERVER,
    [StoreManager::PARAM_RUN_CODE => 'admin', StoreManager::PARAM_RUN_TYPE => 'store']
);
/** @var Magento\Indexer\App\Shell $app */
$app = $bootstrap->createApplication('Magento\Indexer\App\Shell', ['entryFileName' => basename(__FILE__)]);
$bootstrap->run($app);
