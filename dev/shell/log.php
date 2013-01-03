<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Shell
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once __DIR__ . '/../../app/bootstrap.php';
/** @var $app Mage_Core_Model_App */
$app = Mage::getObjectManager()->get('Mage_Core_Model_App');
$app->init(array(
    Mage_Core_Model_App::INIT_OPTION_SCOPE_CODE => 'admin',
    Mage_Core_Model_App::INIT_OPTION_SCOPE_TYPE => 'store',
));

/** @var $shell Mage_Log_Model_Shell */
$shell = Mage::getModel('Mage_Log_Model_Shell', array('entryPoint' => basename(__FILE__)));
$shell->run();
