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
    /** @var $app Mage_Core_Model_App */
    $app = Mage::getObjectManager()->get('Mage_Core_Model_App');
    $app->init(array(Mage_Core_Model_App::INIT_OPTION_SCOPE_CODE => 'admin'));
    $app->setUseSessionInUrl(false);
    $app->requireInstalledInstance();
    $app->addEventArea('crontab');
    Mage::dispatchEvent('default');
} catch (Exception $e) {
    Mage::printException($e);
}
