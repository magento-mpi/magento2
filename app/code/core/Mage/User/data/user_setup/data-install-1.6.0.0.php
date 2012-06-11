<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Save administrators group role and rules
 */
$admGroupRole = Mage::getModel('Mage_User_Model_Role')->setData(array(
    'parent_id'     => 0,
    'tree_level'    => 1,
    'sort_order'    => 1,
    'role_type'     => 'G',
    'user_id'       => 0,
    'role_name'     => 'Administrators'
    ))
    ->save();

Mage::getModel('Mage_User_Model_Rules')->setData(array(
    'role_id'       => $admGroupRole->getId(),
    'resource_id'   => 'all',
    'privileges'    => null,
    'assert_id'     => 0,
    'role_type'     => 'G',
    'permission'    => 'allow'
    ))
    ->save();
