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
\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\App')
    ->loadAreaPart(\Magento\Core\Model\App\Area::AREA_ADMINHTML, \Magento\Core\Model\App\Area::PART_CONFIG);
if (!isset($scope)) {
    $scope = 'websites';
}

/** @var $role \Magento\User\Model\Role */
$role = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\User\Model\Role');
$role->setName('admingws_role')
    ->setGwsIsAll(0)
    ->setRoleType('G')
    ->setPid('1');
if ('websites' == $scope) {
    $role->setGwsWebsites(\Magento\TestFramework\Helper\Bootstrap::getObjectManager()
        ->get('Magento\Core\Model\StoreManagerInterface')->getWebsite()->getId());
} else {
    $role->setGwsStoreGroups(\Magento\TestFramework\Helper\Bootstrap::getObjectManager()
        ->get('Magento\Core\Model\StoreManagerInterface')->getWebsite()->getDefaultGroupId());
}
$role->save();

/** @var $rule \Magento\User\Model\Rules */
$rule = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\User\Model\Rules');
$rule->setRoleId($role->getId())
    ->setResources(array('Magento_Adminhtml::all'))
    ->saveRel();

$user = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\User\Model\User');
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
