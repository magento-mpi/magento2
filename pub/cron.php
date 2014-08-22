<?php
/**
 * Scheduled jobs entry point
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

use Magento\Framework\App\Bootstrap;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManager;

require dirname(__DIR__) . '/app/bootstrap.php';
$params = $_SERVER;
$params[StoreManager::PARAM_RUN_CODE] = 'admin';
$params[Store::CUSTOM_ENTRY_POINT_PARAM] = true;
$bootstrap = new Bootstrap(BP, $params);
/** @var \Magento\Framework\App\Cron $app */
$app = $bootstrap->createApplication('Magento\Framework\App\Cron', ['parameters' => ['group::']]);
$bootstrap->run($app);
