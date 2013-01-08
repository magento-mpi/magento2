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
    Mage::app('admin')->setUseSessionInUrl(false);
    Mage::app()->requireInstalledInstance();
    Mage::getConfig()->init()->loadEventObservers('crontab');
    Mage::app()->addEventArea('crontab');
    Mage::dispatchEvent('default');
} catch (Exception $e) {
    Mage::printException($e);
}
