<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webapi
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$allowResourceId = 'customer/get';
/** @var Magento_Webapi_Model_Acl_Role $role */
$role = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Webapi_Model_Acl_Role');
$role->setData(array(
    'role_name' => 'Test role'
));
$role->save();
/** @var Magento_Webapi_Model_Acl_Rule $rule */
$rule = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Webapi_Model_Acl_Rule');
$rule->setData(array(
    'resource_id' => $allowResourceId,
    'role_id' => $role->getRoleId()
));
$rule->save();
