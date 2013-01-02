<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

if (!Mage::registry('category_on_new_website')) {
    /* @var $rootCategory Mage_Catalog_Model_Category */
    $rootCategory = require '_fixture/_block/Catalog/Category.php';
    $rootCategory->save();

    // create new store fuxture
    require '_fixture/Core/Store/store_on_new_website.php';
    /** @var $storeGroup Mage_Core_Model_Store_Group */
    $storeGroup = Mage::registry('store_group');
    $storeGroup->setRootCategoryId($rootCategory->getId())->save();

    Mage::register(
        'category_on_new_website',
        $rootCategory
    );
}
