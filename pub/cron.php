<?php
/**
 * Scheduled jobs entry point
 *
 * {license_notice}
 *
 * @category   Magento
 * @package    Mage
 * @copyright  {copyright}
 * @license    {license_link}
 */

require dirname(__DIR__) . '/app/bootstrap.php';
Magento_Profiler::start('mage');
umask(0);

try {
    $params = array(
        Mage::PARAM_RUN_CODE => 'admin',
        Magento_Core_Model_Store::CUSTOM_ENTRY_POINT_PARAM => true
    );
    $config = new Magento_Core_Model_Config_Primary(BP, $params);
    $entryPoint = new Magento_Core_Model_EntryPoint_Cron($config);
    $entryPoint->processRequest();
} catch (Exception $e) {
    Mage::printException($e);
}
Magento_Profiler::stop('mage');
