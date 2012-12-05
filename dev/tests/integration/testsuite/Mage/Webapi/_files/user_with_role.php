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
/** @var Mage_Webapi_Model_Acl_Role $role */
$role = Mage::getModel('Mage_Webapi_Model_Acl_Role');
$role->setData(array(
    'role_name' => 'Test role'
));
$role->save();
/** @var Mage_Webapi_Model_Acl_User $user */
$user = Mage::getModel('Mage_Webapi_Model_Acl_User');
$user->setData(array(
    'api_key' => 'test_username',
    'secret' => '123123qa',
    'contact_email' => 'null@null.com',
    'role_id' => $role->getRoleId()
));
$user->save();
