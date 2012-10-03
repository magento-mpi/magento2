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

$role = new Mage_Webapi_Model_Acl_Role();
$role->setData(array(
    'role_name' => 'Test role'
));
$role->save();

$user = new Mage_Webapi_Model_Acl_User();
$user->setData(array(
    'user_name' => 'test_username',
    'api_secret' => '123123qa',
    'role_id' => $role->getRoleId()
));
$user->save();
