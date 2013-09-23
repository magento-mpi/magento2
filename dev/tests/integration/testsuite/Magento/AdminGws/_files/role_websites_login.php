<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdminGws
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_App')
    ->loadAreaPart(Magento_Core_Model_App_Area::AREA_ADMINHTML, Magento_Core_Model_App_Area::PART_CONFIG);
if (!isset($scope)) {
    $scope = 'websites';
}

/** @var $role Magento_User_Model_Role */
$role = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create('Magento_User_Model_Role');
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

/** @var $rule Magento_User_Model_Rules */
$rule = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_User_Model_Rules');
$rule->setRoleId($role->getId())
    ->setResources(array('Magento_Adminhtml::all'))
    ->saveRel();

$user = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_User_Model_User');
$user->setData(array(
    'firstname' => 'firstname',
    'lastname'  => 'lastname',
    'email'     => 'admingws@example.com',
    'username'  => 'admingws_user',
    'password'  => 'admingws_password1',
    'is_active' => 1
));

$user->setRoleId($role->getId())
    ->save();
