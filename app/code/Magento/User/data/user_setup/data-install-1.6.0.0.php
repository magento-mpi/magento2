<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
use Magento\User\Model\Acl\Role\Group as RoleGroup;

/**
 * Save administrators group role and rules
 */

/** @var \Magento\User\Model\Resource\Setup $this */

$roleCollection = $this->createRoleCollection()->addFieldToFilter(
    'parent_id',
    0
)->addFieldToFilter(
    'tree_level',
    1
)->addFieldToFilter(
    'role_type',
    RoleGroup::ROLE_TYPE
)->addFieldToFilter(
    'user_id',
    0
)->addFieldToFilter(
    'role_name',
    'Administrators'
);

if ($roleCollection->count() == 0) {
    $admGroupRole = $this->createRole()->setData(
        array(
            'parent_id' => 0,
            'tree_level' => 1,
            'sort_order' => 1,
            'role_type' => RoleGroup::ROLE_TYPE,
            'user_id' => 0,
            'role_name' => 'Administrators'
        )
    )->save();
} else {
    foreach ($roleCollection as $item) {
        $admGroupRole = $item;
        break;
    }
}

$rulesCollection = $this->createRulesCollection()->addFieldToFilter(
    'role_id',
    $admGroupRole->getId()
)->addFieldToFilter(
    'resource_id',
    'all'
);

if ($rulesCollection->count() == 0) {
    $this->createRules()->setData(
        array(
            'role_id' => $admGroupRole->getId(),
            'resource_id' => 'Magento_Adminhtml::all',
            'privileges' => null,
            'permission' => 'allow'
        )
    )->save();
} else {
    /** @var \Magento\User\Model\Rules $rule */
    foreach ($rulesCollection as $rule) {
        $rule->setData('resource_id', 'Magento_Adminhtml::all')->save();
    }
}
