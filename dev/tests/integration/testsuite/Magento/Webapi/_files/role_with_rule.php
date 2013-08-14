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
$role = Mage::getModel('Magento_Webapi_Model_Acl_Role');
$role->setData(array(
    'role_name' => 'Test role'
));
$role->save();
/** @var Magento_Webapi_Model_Acl_Rule $rule */
$rule = Mage::getModel('Magento_Webapi_Model_Acl_Rule');
$rule->setData(array(
    'resource_id' => $allowResourceId,
    'role_id' => $role->getRoleId()
));
$rule->save();
