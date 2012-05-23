<?php
/**
 * {license_notice}
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
if (!Magento_Test_Webservice::getFixture('store')) {

    $category = new Mage_Catalog_Model_Category();
    $category->setData(array(
        'name' => 'Category Test Created ' . uniqid(),
        'is_active' => 1,
        'is_anchor' => 1,
        'landing_page' => 1, //ID of CMS block
        'position' => 100,
        'description' => 'some description',
        'default_sort_by' => 'name',
        'available_sort_by' => array('name'),
        'display_mode' => Mage_Catalog_Model_Category::DM_PRODUCT,
        'landing_page' => 1, //ID of static block
        'include_in_menu' => 1,
        'page_layout' => 'one_column',
        'custom_design' => 'default/default',
        'custom_design_apply' => 'someValue', //deprecated attribute, should be empty
        'custom_design_from' => '11/16/2011', //date of start use design
        'custom_design_to' => '11/21/2011', //date of finish use design
        'custom_layout_update' => '<block type="core/text_list" name="content" output="toHtml"/>',
        'meta_description' => 'Meta description',
        'meta_keywords' => 'Meta keywords',
        'meta_title' => 'Meta title',
        'url_key' => 'url-key' . uniqid()
    ));
    $parentId = Mage_Catalog_Model_Category::TREE_ROOT_ID;
    $parentCategory = Mage::getModel('catalog/category')->load($parentId);
    $category->setPath($parentCategory->getPath());
    $category->setStoreId(0);
    $category->save();
    Magento_Test_Webservice::setFixture('category', $category);


    $website = new Mage_Core_Model_Website();
    $website->setData(
        array(
            'code' => 'test_' . uniqid(),
            'name' => 'test website' . uniqid()
        )
    );
    $website->save();
    Magento_Test_Webservice::setFixture('website', $website);

    $storeGroup = new Mage_Core_Model_Store_Group();
    $storeGroup->setData(array(
        'website_id' => $website->getId(),
        'name' => 'Test Store' . uniqid(),
        'code' => 'store_group_' . uniqid(),
        'root_category_id' => $category->getId()
    ))->save();
    Magento_Test_Webservice::setFixture('store_group', $storeGroup);


    $store = new Mage_Core_Model_Store();
    $store->setData(array(
        'group_id' => $storeGroup->getId(),
        'name' => 'Test Store View' . uniqid(),
        'code' => 'store_' . uniqid(),
        'is_active' => true,
        'website_id' => $website->getId()
    ))->save();
    Mage::app()->reinitStores();
    Magento_Test_Webservice::setFixture('store', $store);
}
