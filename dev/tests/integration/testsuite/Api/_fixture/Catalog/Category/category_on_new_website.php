<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

if (!Magento_Test_TestCase_ApiAbstract::getFixture('category_on_new_website')) {
    /* @var $rootCategory Mage_Catalog_Model_Category */
    $rootCategory = require 'API/_fixture/_block/Catalog/Category.php';
    $rootCategory->save();

    // create new store fuxture
    require 'API/_fixture/Core/Store/store_on_new_website.php';
    /** @var $storeGroup Mage_Core_Model_Store_Group */
    $storeGroup = Magento_Test_TestCase_ApiAbstract::getFixture('store_group');
    $storeGroup->setRootCategoryId($rootCategory->getId())->save();

    Magento_Test_TestCase_ApiAbstract::setFixture(
        'category_on_new_website',
        $rootCategory,
        Magento_Test_TestCase_ApiAbstract::AUTO_TEAR_DOWN_DISABLED
    );
}
