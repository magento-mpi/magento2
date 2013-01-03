<?php
/**
 * Application entry point
 *
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage
 * @copyright  {copyright}
 * @license    {license_link}
 */

 /**
 * Example - run a particular store or website:
 *
 * $params = $_SERVER;
 * $params['MAGE_RUN_CODE'] = 'website2';
 * $params['MAGE_RUN_TYPE'] = 'website';
 * ...
 * $app->run($params)
 */
try {
    require __DIR__ . '/app/bootstrap.php';
    /** @var $app Mage_Core_Model_App */
    $app = Mage::getObjectManager()->get('Mage_Core_Model_App');
    $app->run($_SERVER);
} catch (Exception $e) {
    Mage::printException($e);
}

