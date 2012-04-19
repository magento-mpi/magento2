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

$roleData = require 'roleData.php';
/** @var $role Mage_Api2_Model_Acl_Global_Role */
$role = Mage::getModel('api2/acl_global_role');
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
