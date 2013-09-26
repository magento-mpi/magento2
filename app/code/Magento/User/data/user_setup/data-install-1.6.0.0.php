<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Save administrators group role and rules
 */

/** @var Magento_User_Model_Resource_Setup $this */

$roleCollection = $this->createRoleCollection()
    ->addFieldToFilter('parent_id', 0)
    ->addFieldToFilter('tree_level', 1)
    ->addFieldToFilter('role_type', 'G')
    ->addFieldToFilter('user_id', 0)
    ->addFieldToFilter('role_name', 'Administrators');

if ($roleCollection->count() == 0) {
    $admGroupRole = $this->createRole()->setData(array(
        'parent_id'     => 0,
        'tree_level'    => 1,
        'sort_order'    => 1,
        'role_type'     => 'G',
        'user_id'       => 0,
        'role_name'     => 'Administrators'
    ))
    ->save();
} else {
    foreach ($roleCollection as $item) {
        $admGroupRole = $item;
        break;
    }
}

$rulesCollection = $this->createRulesCollection()
    ->addFieldToFilter('role_id', $admGroupRole->getId())
    ->addFieldToFilter('resource_id', 'all')
    ->addFieldToFilter('role_type', 'G');

if ($rulesCollection->count() == 0) {
    $this->createRules()->setData(array(
        'role_id'       => $admGroupRole->getId(),
        'resource_id'   => 'Magento_Adminhtml::all',
        'privileges'    => null,
        'role_type'     => 'G',
        'permission'    => 'allow'
        ))
    ->save();
} else {
    /** @var Magento_User_Model_Rules $rule */
    foreach ($rulesCollection as $rule) {
        $rule->setData('resource_id', 'Magento_Adminhtml::all')
            ->save();
    }
}
