<?php
/**
 * Scheduled jobs entry point
 *
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento
 * @copyright  {copyright}
 * @license    {license_link}
 */
use Magento\Store\Model\StoreManager;


require dirname(__DIR__) . '/app/bootstrap.php';
umask(0);
$params = array(StoreManager::PARAM_RUN_CODE => 'admin', \Magento\Store\Model\Store::CUSTOM_ENTRY_POINT_PARAM => true);
$entryPoint = new \Magento\Framework\App\EntryPoint\EntryPoint(BP, $params);
$entryPoint->run('Magento\Framework\App\Cron', array('parameters' => array('group::')));
