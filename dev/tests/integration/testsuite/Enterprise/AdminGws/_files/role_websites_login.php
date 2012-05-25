<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_AdminGws
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

if (!isset($scope)) {
    $scope = 'websites';
}

$role = new Mage_User_Model_Role;
$role->setName('admingws_role')
    ->setGwsIsAll(0)
    ->setRoleType('G')
    ->setPid('1');
if ('websites' == $scope) {
    $role->setGwsWebsites(Mage::app()->getWebsite()->getId());
} else {
    $role->setGwsStoreGroups(Mage::app()->getWebsite()->getDefaultGroupId());
}
$role->save();

$rule = new Mage_User_Model_Rules;
$rule->setRoleId($role->getId())
    ->setResources(array('all'))
    ->saveRel();

$user = new Mage_User_Model_User();
$user->setData(array(
    'firstname' => 'firstname',
    'lastname'  => 'lastname',
    'email'     => 'admingws@example.com',
    'username'  => 'admingws_user',
    'password'  => 'admingws_password',
    'is_active' => 1
));

$user->setRoleId($role->getId())
    ->save();
