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
/** @var \Magento\Webapi\Model\Acl\Role $role */
$role = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Webapi\Model\Acl\Role');
$role->setData(array(
    'role_name' => 'Test role'
));
$role->save();
/** @var \Magento\Webapi\Model\Acl\Rule $rule */
$rule = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Webapi\Model\Acl\Rule');
$rule->setData(array(
    'resource_id' => $allowResourceId,
    'role_id' => $role->getRoleId()
));
$rule->save();
