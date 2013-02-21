<?php
/**
 * Scheduled jobs entry point
 *
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage
 * @copyright  {copyright}
 * @license    {license_link}
 */

require dirname(__DIR__) . '/app/bootstrap.php';

Mage::register('custom_entry_point', true);
umask(0);

try {
    $params = array(Mage::PARAM_RUN_CODE => 'admin');
    $entryPoint = new Mage_Core_Model_EntryPoint_Cron(BP, $params);
    $entryPoint->processRequest();
} catch (Exception $e) {
    Mage::printException($e);
}
