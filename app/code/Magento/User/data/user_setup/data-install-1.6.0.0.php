<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Save administrators group role and rules
 */

/** @var $roleCollection \Magento\User\Model\Resource\Role\Collection */
$roleCollection = \Mage::getModel('Magento\User\Model\Role')->getCollection()
    ->addFieldToFilter('parent_id', 0)
    ->addFieldToFilter('tree_level', 1)
    ->addFieldToFilter('role_type', 'G')
    ->addFieldToFilter('user_id', 0)
    ->addFieldToFilter('role_name', 'Administrators');

if ($roleCollection->count() == 0) {
    $admGroupRole = \Mage::getModel('Magento\User\Model\Role')->setData(array(
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

/** @var $rulesCollection \Magento\User\Model\Resource\Rules\Collection */
$rulesCollection = \Mage::getModel('Magento\User\Model\Rules')->getCollection()
    ->addFieldToFilter('role_id', $admGroupRole->getId())
    ->addFieldToFilter('resource_id', 'all')
    ->addFieldToFilter('role_type', 'G');

if ($rulesCollection->count() == 0) {
    \Mage::getModel('Magento\User\Model\Rules')->setData(array(
        'role_id'       => $admGroupRole->getId(),
        'resource_id'   => 'Magento_Adminhtml::all',
        'privileges'    => null,
        'role_type'     => 'G',
        'permission'    => 'allow'
        ))
    ->save();
} else {
    foreach ($rulesCollection as $rule) {
        $rule->setData('resource_id', 'Magento_Adminhtml::all')
            ->save();
    }
}
