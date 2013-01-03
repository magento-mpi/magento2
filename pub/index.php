<?php
/**
 * Public alias for the application entry point
 *
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage
 * @copyright  {copyright}
 * @license    {license_link}
 */

require __DIR__ . '/../app/bootstrap.php';
$params = $_SERVER;
$params[Mage_Core_Model_App::INIT_OPTION_URIS][Mage_Core_Model_Dir::PUB] = '';
try {
    /** @var $app Mage_Core_Model_App */
    $app = Mage::getObjectManager()->get('Mage_Core_Model_App');
    $app->run($params);
} catch (Exception $e) {
    Mage::printException($e);
}
