<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
if (!Mage::registry('website')) {
    $website = Mage::getModel('Magento_Core_Model_Website');
    $website->setData(
        array(
            'code' => 'test_' . uniqid(),
            'name' => 'test website',
            'default_group_id' => 1,
        )
    );
    $website->save();
    Mage::register('website', $website);
}

if (!Mage::registry('store_group')) {
    $defaultCategoryId = 2;
    $storeGroup = Mage::getModel('Magento_Core_Model_Store_Group');
    $storeGroup->setData(
        array(
            'website_id' => Mage::registry('website')->getId(),
            'name' => 'Test Store' . uniqid(),
            'code' => 'store_group_' . uniqid(),
            'root_category_id' => $defaultCategoryId
        )
    )->save();
    Mage::register('store_group', $storeGroup);
}

if (!Mage::registry('store_on_new_website')) {
    $store = Mage::getModel('Magento_Core_Model_Store');
    $store->setData(
        array(
            'group_id' => Mage::registry('store_group')->getId(),
            'name' => 'Test Store View',
            'code' => 'store_' . uniqid(),
            'is_active' => true,
            'website_id' => Mage::registry('website')->getId()
        )
    )->save();
    Mage::register('store_on_new_website', $store);
    Mage::app()->reinitStores();
}
