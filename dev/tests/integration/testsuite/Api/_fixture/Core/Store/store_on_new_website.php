<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
if (!Magento_Test_TestCase_ApiAbstract::getFixture('website')) {
    $website = Mage::getModel('Mage_Core_Model_Website');
    $website->setData(
        array(
            'code' => 'test_' . uniqid(),
            'name' => 'test website',
            'default_group_id' => 1,
        )
    );
    $website->save();
    Magento_Test_TestCase_ApiAbstract::setFixture(
        'website',
        $website,
        Magento_Test_TestCase_ApiAbstract::AUTO_TEAR_DOWN_DISABLED
    );
}

if (!Magento_Test_TestCase_ApiAbstract::getFixture('store_group')) {
    $defaultCategoryId = 2;
    $storeGroup = Mage::getModel('Mage_Core_Model_Store_Group');
    $storeGroup->setData(
        array(
            'website_id' => Magento_Test_TestCase_ApiAbstract::getFixture('website')->getId(),
            'name' => 'Test Store' . uniqid(),
            'code' => 'store_group_' . uniqid(),
            'root_category_id' => $defaultCategoryId
        )
    )->save();
    Magento_Test_TestCase_ApiAbstract::setFixture(
        'store_group',
        $storeGroup,
        Magento_Test_TestCase_ApiAbstract::AUTO_TEAR_DOWN_DISABLED
    );
}

if (!Magento_Test_TestCase_ApiAbstract::getFixture('store_on_new_website')) {
    $store = Mage::getModel('Mage_Core_Model_Store');
    $store->setData(
        array(
            'group_id' => Magento_Test_TestCase_ApiAbstract::getFixture('store_group')->getId(),
            'name' => 'Test Store View',
            'code' => 'store_' . uniqid(),
            'is_active' => true,
            'website_id' => Magento_Test_TestCase_ApiAbstract::getFixture('website')->getId()
        )
    )->save();
    Magento_Test_TestCase_ApiAbstract::setFixture(
        'store_on_new_website',
        $store,
        Magento_Test_TestCase_ApiAbstract::AUTO_TEAR_DOWN_DISABLED
    );
    Mage::app()->reinitStores();
}
