<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require 'testsuite/Mage/Admin/_files/user.php';
Mage::getSingleton('Mage_Adminhtml_Model_Url')->turnOffSecretKey();

$session = new Mage_DesignEditor_Model_Session();
$session->login('user', 'password');
$session->activateDesignEditor();
