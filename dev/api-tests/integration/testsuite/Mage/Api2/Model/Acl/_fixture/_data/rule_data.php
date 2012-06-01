<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Api2
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Rule data fixture
 */

$roleData = require 'role_data.php';
/** @var $role Mage_Api2_Model_Acl_Global_Role */
$role = Mage::getModel('Mage_Api2_Model_Acl_Global_Role');
$role->setData($roleData['create']);
$role->save();

$permissions = Mage_Api2_Model_Acl_Global_Rule_Permission::toArray();
$roleId = $role->getId();
return array(
    'create' => array(
        'role_id'     => $roleId,
        'permission'  => Mage_Api2_Model_Acl_Global_Rule_Permission::TYPE_ALLOW,
        'resource_id' => 'some/resource',
    ),
    'update' => array(
        'role_id'     => $roleId,
        'permission'  => Mage_Api2_Model_Acl_Global_Rule_Permission::TYPE_DENY,
        'resource_id' => 'someUpdate/resource',
    )
);
