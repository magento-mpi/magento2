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
Mage::app('admin', 'store');

/** @var $shell Mage_Log_Model_Shell */
$shell = Mage::getModel('Mage_Log_Model_Shell', array('entryPoint' => basename(__FILE__)));
$shell->run();
