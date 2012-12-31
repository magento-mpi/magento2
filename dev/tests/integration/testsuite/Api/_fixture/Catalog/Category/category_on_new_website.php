<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

if (!PHPUnit_Framework_TestCase::getFixture('category_on_new_website')) {
    /* @var $rootCategory Mage_Catalog_Model_Category */
    $rootCategory = require '_fixture/_block/Catalog/Category.php';
    $rootCategory->save();

    // create new store fuxture
    require '_fixture/Core/Store/store_on_new_website.php';
    /** @var $storeGroup Mage_Core_Model_Store_Group */
    $storeGroup = PHPUnit_Framework_TestCase::getFixture('store_group');
    $storeGroup->setRootCategoryId($rootCategory->getId())->save();

    PHPUnit_Framework_TestCase::setFixture(
        'category_on_new_website',
        $rootCategory,
        PHPUnit_Framework_TestCase::AUTO_TEAR_DOWN_DISABLED
    );
}
