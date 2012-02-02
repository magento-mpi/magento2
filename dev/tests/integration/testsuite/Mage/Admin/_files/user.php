<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$role = new Mage_Admin_Model_Role();
$role->setData(array(
    'parent_id'     => 0,
    'tree_level'    => 1,
    'sort_order'    => 1,
    'role_type'     => 'G',
    'user_id'       => 0,
    'role_name'     => 'Test Role'
    ))
    ->save();


$user = new Mage_Admin_Model_User();
$user->setResourceId('all')
    ->setFirstname('firstname')
    ->setLastname('lastname')
    ->setEmail('email@magento.com')
    ->setUsername('user')
    ->setPassword('password')
    ->setIsActive(1)
    ->save();

$user->setRoleIds(array($role->getId()))
    ->saveRelations();
