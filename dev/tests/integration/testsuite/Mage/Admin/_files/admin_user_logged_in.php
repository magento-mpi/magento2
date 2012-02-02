<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Admin
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/admin_user.php';

$user = new Mage_Admin_Model_User();
$user->loadByUsername('admin');

$session = new Mage_Admin_Model_Session();
$session->setData('user', $user);
$session->setData('acl', Mage::getResourceModel('Mage_Admin_Model_Resource_Acl')->loadAcl());
