<?php
/**
 * Scheduled jobs entry point
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManager;

/** @var \Magento\Framework\App\Bootstrap $bootstrap */
$bootstrap = require dirname(__DIR__) . '/app/bootstrap.php';
$bootstrap->addParams([StoreManager::PARAM_RUN_CODE => 'admin', Store::CUSTOM_ENTRY_POINT_PARAM => true]);
/** @var \Magento\Framework\App\Cron $app */
$app = $bootstrap->createApplication('Magento\Framework\App\Cron', ['parameters' => ['group::']]);
$bootstrap->run($app);
