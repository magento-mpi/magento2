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
Mage::register('custom_entry_point', true);
umask(0);

try {
    $params = array(Mage::PARAM_RUN_CODE => 'admin');
    $config = new Magento_Core_Model_Config_Primary(BP, $params);
    $entryPoint = new Magento_Core_Model_EntryPoint_Cron($config);
    $entryPoint->processRequest();
} catch (Exception $e) {
    Mage::printException($e);
}
Magento_Profiler::stop('mage');
