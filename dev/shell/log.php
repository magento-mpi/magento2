<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shell
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once __DIR__ . '/../../app/bootstrap.php';
use Magento\Store\Model\StoreManager;

$params = array(
    StoreManager::PARAM_RUN_CODE => 'admin',
    StoreManager::PARAM_RUN_TYPE => 'store'
);

$entryPoint = new \Magento\Framework\App\EntryPoint\EntryPoint(BP, $params);
$entryPoint->run('Magento\Log\App\Shell', array('entryFileName' => basename(__FILE__)));
