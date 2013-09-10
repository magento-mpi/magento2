<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
/** @var $objectManager Magento_TestFramework_ObjectManager */
$objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
if (!$objectManager->get('Magento_Core_Model_Registry')->registry('website')) {
    $website = Mage::getModel('Magento_Core_Model_Website');
    $website->setData(
        array(
            'code' => 'test_' . uniqid(),
            'name' => 'test website',
            'default_group_id' => 1,
        )
    );
    $website->save();
    $objectManager->get('Magento_Core_Model_Registry')->register('website', $website);
}

if (!$objectManager->get('Magento_Core_Model_Registry')->registry('store_group')) {
    $defaultCategoryId = 2;
    $storeGroup = Mage::getModel('Magento_Core_Model_Store_Group');
    $storeGroup->setData(
        array(
            'website_id' => $objectManager->get('Magento_Core_Model_Registry')->registry('website')->getId(),
            'name' => 'Test Store' . uniqid(),
            'code' => 'store_group_' . uniqid(),
            'root_category_id' => $defaultCategoryId
        )
    )->save();
    $objectManager->get('Magento_Core_Model_Registry')->register('store_group', $storeGroup);
}

if (!$objectManager->get('Magento_Core_Model_Registry')->registry('store_on_new_website')) {
    $store = Mage::getModel('Magento_Core_Model_Store');
    $store->setData(
        array(
            'group_id' => $objectManager->get('Magento_Core_Model_Registry')->registry('store_group')->getId(),
            'name' => 'Test Store View',
            'code' => 'store_' . uniqid(),
            'is_active' => true,
            'website_id' => $objectManager->get('Magento_Core_Model_Registry')->registry('website')->getId()
        )
    )->save();
    $objectManager->get('Magento_Core_Model_Registry')->register('store_on_new_website', $store);
    Mage::app()->reinitStores();
}
