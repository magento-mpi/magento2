<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
if (!Mage::registry('website')) {
    $website = Mage::getModel('Mage_Core_Model_Website');
    $website->setData(
        array(
            'code' => 'test_' . uniqid(),
            'name' => 'test website',
            'default_group_id' => 1,
        )
    );
    $website->save();
    PHPUnit_Framework_TestCase::setFixture(
        'website',
        $website,
        PHPUnit_Framework_TestCase::AUTO_TEAR_DOWN_DISABLED
    );
}

if (!Mage::registry('store_group')) {
    $defaultCategoryId = 2;
    $storeGroup = Mage::getModel('Mage_Core_Model_Store_Group');
    $storeGroup->setData(
        array(
            'website_id' => Mage::registry('website')->getId(),
            'name' => 'Test Store' . uniqid(),
            'code' => 'store_group_' . uniqid(),
            'root_category_id' => $defaultCategoryId
        )
    )->save();
    PHPUnit_Framework_TestCase::setFixture(
        'store_group',
        $storeGroup,
        PHPUnit_Framework_TestCase::AUTO_TEAR_DOWN_DISABLED
    );
}

if (!Mage::registry('store_on_new_website')) {
    $store = Mage::getModel('Mage_Core_Model_Store');
    $store->setData(
        array(
            'group_id' => Mage::registry('store_group')->getId(),
            'name' => 'Test Store View',
            'code' => 'store_' . uniqid(),
            'is_active' => true,
            'website_id' => Mage::registry('website')->getId()
        )
    )->save();
    PHPUnit_Framework_TestCase::setFixture(
        'store_on_new_website',
        $store,
        PHPUnit_Framework_TestCase::AUTO_TEAR_DOWN_DISABLED
    );
    Mage::app()->reinitStores();
}
