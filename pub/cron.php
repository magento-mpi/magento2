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
\Magento\Profiler::start('mage');
umask(0);

try {
    $params = array(
        Mage::PARAM_RUN_CODE => 'admin',
        Magento\Core\Model\Store::CUSTOM_ENTRY_POINT_PARAM => true
    );
    $config = new \Magento\Core\Model\Config\Primary(BP, $params);
    $entryPoint = new \Magento\Core\Model\EntryPoint\Cron($config);
    $entryPoint->processRequest();
} catch (Exception $e) {
    Mage::printException($e);
}
\Magento\Profiler::stop('mage');
