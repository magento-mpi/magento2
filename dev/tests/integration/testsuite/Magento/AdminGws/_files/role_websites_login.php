<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
    'Magento\Framework\App\AreaList'
)->getArea(
    \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE
)->load(
    \Magento\Framework\App\Area::PART_CONFIG
);
if (!isset($scope)) {
    $scope = 'websites';
}

/** @var $role \Magento\Authorization\Model\Role */
$role = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Authorization\Model\Role');
$role->setName('admingws_role')->setGwsIsAll(0)->setRoleType('G')->setPid('1');
if ('websites' == $scope) {
    $role->setGwsWebsites(
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Store\Model\StoreManagerInterface'
        )->getWebsite()->getId()
    );
} else {
    $role->setGwsStoreGroups(
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Store\Model\StoreManagerInterface'
        )->getWebsite()->getDefaultGroupId()
    );
}
$role->save();

/** @var $rule \Magento\Authorization\Model\Rules */
$rule = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Authorization\Model\Rules');
$rule->setRoleId($role->getId())->setResources(['Magento_Adminhtml::all'])->saveRel();

$user = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\User\Model\User');
$user->setData(
    [
        'firstname' => 'firstname',
        'lastname' => 'lastname',
        'email' => 'admingws@example.com',
        'username' => 'admingws_user',
        'password' => 'admingws_password1',
        'is_active' => 1,
    ]
);

$user->setRoleId($role->getId())->save();
