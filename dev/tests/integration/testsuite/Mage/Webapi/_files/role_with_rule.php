<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$allowResourceId = 'customer/get';
/** @var Mage_Webapi_Model_Acl_Role $role */
$role = Mage::getModel('Mage_Webapi_Model_Acl_Role');
$role->setData(array(
    'role_name' => 'Test role'
));
$role->save();
/** @var Mage_Webapi_Model_Acl_Rule $rule */
$rule = Mage::getModel('Mage_Webapi_Model_Acl_Rule');
$rule->setData(array(
    'resource_id' => $allowResourceId,
    'role_id' => $role->getRoleId()
));
$rule->save();
