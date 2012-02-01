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

$user = new Mage_Admin_Model_User();
$user->setData(array(
    'firstname' => 'Admin',
    'lastname'  => 'Admin',
    'email'     => 'admin@example.com',
    'username'  => 'admin',
    'password'  => '123123q',
));
$user->save();

$roleAdmin = new Mage_Admin_Model_Role();
$roleAdmin->load('Administrators', 'role_name');

$roleUser = new Mage_Admin_Model_Role();
$roleUser->setData(array(
    'parent_id'  => $roleAdmin->getId(),
    'tree_level' => $roleAdmin->getTreeLevel() + 1,
    'role_type'  => Mage_Admin_Model_Acl::ROLE_TYPE_USER,
    'user_id'    => $user->getId(),
    'role_name'  => $user->getFirstname(),
));
$roleUser->save();
