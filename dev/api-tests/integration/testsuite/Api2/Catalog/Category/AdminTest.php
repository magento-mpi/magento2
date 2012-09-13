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

/**
 * Test category resource for admin
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api2_Catalog_Category_AdminTest extends Magento_Test_Webservice_Rest_Admin
{
    /**
     * Delete fixtures
     */
    protected function tearDown()
    {
        self::deleteFixture('category', true);
        self::deleteFixture('root_category', true);
        self::deleteFixture('store', true);
        $categoryTree = $this->getFixture('category_tree');
        if ($categoryTree) {
            foreach ($categoryTree as $category) {
                $this->callModelDelete($category, true);
            }
        }
        parent::tearDown();
    }

    /**
     * Store-related fixtures removal
     */
    public static function tearDownAfterClass()
    {
        self::deleteFixture('store_on_new_website', true);
        self::deleteFixture('store_group', true);
        self::deleteFixture('website', true);
        self::deleteFixture('category_on_new_website', true);
        parent::tearDownAfterClass();
    }

    /**
     * Test successful category get
     *
     * @magentoDataFixture fixture/Catalog/Category/category.php
     * @resourceOperation category::get
     */
    public function testGet()
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = $this->getFixture('category');
        $restResponse = $this->callGet($this->_getResourcePath($category->getId()));
        $this->assertEquals(Mage_Api2_Controller_Front_Rest::HTTP_OK, $restResponse->getStatus());
        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);
        $originalData = $category->getData();
        $originalData['url_path'] = $originalData['url_key'] . '.html';
        foreach ($responseData as $field => $value) {
            if (!is_array($value)) {
                $this->assertEquals($originalData[$field], $value);
            }
        }
    }

    /**
     * Test successful category get with multi store
     *
     * @magentoDataFixture fixture/Catalog/Category/category_multistore.php
     * @resourceOperation category::get
     */
    public function testGetMultiStore()
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = $this->getFixture('category');
        /** @var $store Mage_Core_Model_Store */
        $store = $this->getFixture('store');
        $restResponse = $this->callGet($this->_getResourcePath($category->getId(), $store->getId()));
        $this->assertEquals(Mage_Api2_Controller_Front_Rest::HTTP_OK, $restResponse->getStatus());
        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);
        $inequalFields = array('custom_design_apply', 'path_ids');
        $nullFields = array('custom_design_apply', 'path', 'store_id', 'entity_type_id', 'path_ids',
            'is_changed_product_list', 'updated_at', 'custom_design_from_is_formated', 'custom_design_to_is_formated',
            'level', 'created_at', 'position', 'attribute_set_id');
        $this->_checkCategoryData($responseData, $category->getData(), $nullFields, $inequalFields);
    }

    /**
     * Test category get invalid id
     *
     * @resourceOperation category::get
     */
    public function testGetInvalidId()
    {
        $restResponse = $this->callGet($this->_getResourcePath('INVALID_ID'));
        $this->assertEquals(Mage_Api2_Controller_Front_Rest::HTTP_NOT_FOUND, $restResponse->getStatus());
    }

    /**
     * Test successful category tree get
     *
     * @magentoDataFixture fixture/Catalog/Category/category_tree.php
     * @resourceOperation category::multiget
     */
    public function testGetCategoriesTree()
    {
        $categoryTree = $this->getFixture('category_tree');
        /** @var $parentCategory Mage_Catalog_Model_Category */
        $parentCategory = reset($categoryTree);
        $restResponse = $this->callGet($this->_getResourcePath(), array('root' => $parentCategory->getId()));
        $this->assertEquals(Mage_Api2_Controller_Front_Rest::HTTP_OK, $restResponse->getStatus());
        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);

        $responseParentCategory = reset($responseData);
        $expectedCategories = array();
        /** @var $category Mage_Catalog_Model_Category */
        foreach ($categoryTree as $category) {
            $expectedCategories[] = $category->getId();
        }

        $subcategoriesCount = $this->_checkCategoryTree($responseParentCategory, $expectedCategories);
        $this->assertEquals($subcategoriesCount, count($categoryTree) - 1);
    }

    /**
     * Test successful category creation
     *
     * @dataProvider dataProviderOfValidData
     * @resourceOperation category::create
     * @param array $categoryData
     * @param bool $checkData
     */
    public function testPostValidData($categoryData, $checkData = true)
    {
        $createdCategory = $this->_createCategoryWithPost($categoryData);
        if ($checkData) {
            $this->_checkCategoryData($createdCategory->getData(), $categoryData);
        }
    }

    /**
     * Test category data post with 'use config' options set for eligible attributes
     *
     * @dataProvider dataProviderForPostWithUseConfig
     * @resourceOperation category::create
     * @param array $categoryData
     * @param array $attributesToCheck
     */
    public function testPostWithUseConfig(array $categoryData, array $attributesToCheck)
    {
        $createdCategory = $this->_createCategoryWithPost($categoryData);
        foreach ($attributesToCheck as $attributeCode => $expectedAttributeValue) {
            $this->assertThat($createdCategory->getData($attributeCode), $this->identicalTo($expectedAttributeValue),
                "'$attributeCode' attribute value is invalid");
        }
    }

    /**
     * Test unsuccessful category creation
     *
     * @dataProvider dataProviderForPostInvalidData
     * @resourceOperation category::create
     * @param array $testData
     */
    public function testPostInvalidData($testData)
    {
        $restResponse = $this->callPost($this->_getResourcePath(), $testData['data']);
        $this->assertEquals(Mage_Api2_Controller_Front_Rest::HTTP_BAD_REQUEST, $restResponse->getStatus(),
            "Invalid response code");
        $body = $restResponse->getBody();
        $errors = $body['messages']['error'];
        $errorMessages = array();
        foreach ($errors as $error) {
            $errorMessages[] = $error['message'];
        }
        $this->_checkErrorMessages($testData, $errorMessages);
    }

    /**
     * Test successful category creation with image and thumbnail
     *
     * @dataProvider dataProviderOfValidDataWithImages
     * @resourceOperation category::create
     * @param array $images
     */
    public function testPostValidDataWithImages($images)
    {
        $this->_getValidCategoryData();
        $categoryData = array_merge($this->_getValidCategoryData(), $images);
        $createdCategory = $this->_createCategoryWithPost($categoryData);
        $this->assertNotEmpty($createdCategory->getId(), "Created category could not be loaded");
        foreach ($images as $imageAttribute => $image) {
            if ($image['file_name_is_expected_to_be_changed']) {
                $this->assertNotEquals($image['file_name'] . ".png", $createdCategory->getData($imageAttribute),
                    "Unique value was supposed to be generated for attribute '$imageAttribute' instead of given one");
            } else {
                $this->assertEquals($image['file_name'] . ".png", $createdCategory->getData($imageAttribute),
                    "Image attribute '$imageAttribute' has invalid value");
            }
            $imageUrl = Mage::getBaseUrl('media') . 'catalog/category/' . $createdCategory->getData($imageAttribute);
            $this->_testUrlWithCurl($imageUrl, $imageAttribute);
        }
    }

    /**
     * Test post category on specified store
     *
     * @magentoDataFixture fixture/Core/Store/store.php
     * @resourceOperation category::create
     */
    public function testPostCategoryOnSpecifiedStore()
    {
        /** @var $store Mage_Core_Model_Store */
        $store = $this->getFixture('store');
        $categoryData = $this->_getValidCategoryData();
        $createdCategory = $this->_createCategoryWithPost($categoryData, $store->getId());
        $expectedData = array_merge($categoryData, array('store_id' => $store->getId()));
        $this->_checkCategoryData($createdCategory->getData(), $expectedData);

        // check that data was not set in default store
        /** @var $categoryInDefaultStore Mage_Catalog_Model_Category */
        $categoryInDefaultStore = Mage::getModel('Mage_Catalog_Model_Category')->load($createdCategory->getId());
        $nullFields = array_keys(array_diff_key($expectedData, $categoryInDefaultStore->getData()));
        array('store_id', 'description', 'meta_keywords', 'meta_description',
            'custom_layout_update', 'landing_page', 'meta_title', 'display_mode', 'custom_design', 'page_layout',
            'custom_design_from', 'custom_design_to', 'filter_price_range');
        $inequalFields = array('url_key');
        $this->_checkCategoryData($categoryInDefaultStore->getData(), $expectedData, $nullFields, $inequalFields);
    }

    /**
     * Make sure that root category creation is impossible when store is specified
     *
     * @magentoDataFixture fixture/Core/Store/store.php
     * @resourceOperation category::create
     */
    public function testPostRootCategoryWithinSpecifiedStore()
    {
        /** @var $store Mage_Core_Model_Store */
        $store = $this->getFixture('store');
        $categoryData = $this->_getValidCategoryData();
        $categoryData['parent_id'] = Mage_Catalog_Model_Category::TREE_ROOT_ID;
        $restResponse = $this->callPost($this->_getResourcePath(null, $store->getId()), $categoryData);
        $this->assertEquals(Mage_Api2_Controller_Front_Rest::HTTP_BAD_REQUEST, $restResponse->getStatus(),
            "Invalid response code");

        $body = $restResponse->getBody();
        $this->assertTrue(isset($body['messages']['error']), "Error messages expected to be set");
        $expectedErrorMessage = "Root category cannot be created in the scope of specific store.";
        $error = reset($body['messages']['error']);
        $this->assertEquals($expectedErrorMessage, $error['message'], "Invalid error message");
    }

    /**
     * Make sure that category creation on specified website (one of its stores) is impossible
     * when parent category does not belong to this website
     *
     * @magentoDataFixture fixture/Catalog/Category/category_on_new_website.php
     * @resourceOperation category::create
     */
    public function testPostRootCategoryWithInvalidParentCategory()
    {
        $store = Mage::app()->getDefaultStoreView();
        $categoryData = $this->_getValidCategoryData();
        /** @var $categoryOnNewWebsite Mage_Catalog_Model_Category */
        $categoryOnNewWebsite = $this->getFixture('category_on_new_website');
        $categoryData['parent_id'] = $categoryOnNewWebsite->getId();
        $restResponse = $this->callPost($this->_getResourcePath(null, $store->getId()), $categoryData);

        $expectedErrorMessage = "The specified parent category does not match the specified store.";
        $this->_checkErrorMessagesInResponse($restResponse, $expectedErrorMessage);
    }

    /**
     * Test successful category update
     *
     * @magentoDataFixture fixture/Catalog/Category/category.php
     * @dataProvider dataProviderOfValidData
     * @resourceOperation category::update
     * @param array $categoryData
     * @param bool $checkData
     */
    public function testPutValidData($categoryData, $checkData = true)
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = $this->getFixture('category');
        $this->_updateCategoryWithPut($category->getId(), $categoryData);
        /** @var $updatedCategory Mage_Catalog_Model_Category */
        $updatedCategory = Mage::getModel('Mage_Catalog_Model_Category')->load($category->getId());
        if ($checkData) {
            $this->_checkCategoryData($updatedCategory->getData(), $categoryData);
        }
    }

    /**
     * Test successful category update with image and thumbnail
     *
     * @magentoDataFixture fixture/Catalog/Category/category.php
     * @dataProvider dataProviderOfValidDataWithImages
     * @resourceOperation category::update
     * @param array $images
     */
    public function testPutValidDataWithImages($images)
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = $this->getFixture('category');
        $categoryData = array_merge($this->_getValidCategoryData(), $images);
        $this->_updateCategoryWithPut($category->getId(), $categoryData);
        /** @var $updatedCategory Mage_Catalog_Model_Category */
        $updatedCategory = Mage::getModel('Mage_Catalog_Model_Category')->load($category->getId());

        foreach ($images as $imageAttribute => $image) {
            if ($image['file_name_is_expected_to_be_changed']) {
                $this->assertNotEquals($image['file_name'] . ".png", $updatedCategory->getData($imageAttribute),
                    "Unique value was supposed to be generated for attribute '$imageAttribute' instead of given one");
            } else {
                $this->assertEquals($image['file_name'] . ".png", $updatedCategory->getData($imageAttribute),
                    "Image attribute '$imageAttribute' has invalid value");
            }
            $imageUrl = Mage::getBaseUrl('media') . 'catalog/category/' . $updatedCategory->getData($imageAttribute);
            $this->_testUrlWithCurl($imageUrl, $imageAttribute);
            $this->assertNotEquals($updatedCategory->getData($imageAttribute), $category->getData($imageAttribute),
                "Image was not updated");
        }
    }

    /**
     * Test successful category with images update using data without images
     *
     * @magentoDataFixture fixture/Catalog/Category/category.php
     * @resourceOperation category::update
     */
    public function testPutUpdateWithoutImages()
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = $this->getFixture('category');
        $category->setData(array('image' => 'some_not_existing_image.jpg',
            'thumbnail' => 'some_not_existing_thumbnail.jpg'));
        $category->save();

        $categoryData = $this->_getValidCategoryData();
        $this->_updateCategoryWithPut($category->getId(), $categoryData);
    }

    /**
     * Test unsuccessful category update
     *
     * @magentoDataFixture fixture/Catalog/Category/category.php
     * @dataProvider dataProviderForPutInvalidData
     * @resourceOperation category::update
     * @param array $testData
     */
    public function testPutInvalidData($testData)
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = $this->getFixture('category');
        $restResponse = $this->callPut($this->_getResourcePath($category->getId()), $testData['data']);
        $this->assertEquals(Mage_Api2_Controller_Front_Rest::HTTP_BAD_REQUEST, $restResponse->getStatus(),
            "Invalid response code");
        $body = $restResponse->getBody();
        $errors = $body['messages']['error'];
        $errorMessages = array();
        foreach ($errors as $error) {
            $errorMessages[] = $error['message'];
        }
        $this->_checkErrorMessages($testData, $errorMessages);
    }

    /**
     * Test category data update with 'use config' options set for eligible attributes
     *
     * @magentoDataFixture fixture/Catalog/Category/category.php
     * @dataProvider dataProviderForPostWithUseConfig
     * @resourceOperation category::update
     * @param array $categoryData
     * @param array $attributesToCheck
     */
    public function testPutWithUseConfig(array $categoryData, array $attributesToCheck)
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = $this->getFixture('category');
        $this->_updateCategoryWithPut($category->getId(), $categoryData);
        /** @var $updatedCategory Mage_Catalog_Model_Category */
        $updatedCategory = Mage::getModel('Mage_Catalog_Model_Category')->load($category->getId());

        foreach ($attributesToCheck as $attributeCode => $expectedAttributeValue) {
            $this->assertThat($updatedCategory->getData($attributeCode), $this->identicalTo($expectedAttributeValue),
                "'$attributeCode' attribute value is invalid");
        }
    }

    /**
     * Test update category on specified store
     *
     * @magentoDataFixture fixture/Catalog/Category/category.php
     * @magentoDataFixture fixture/Core/Store/store.php
     * @resourceOperation category::update
     */
    public function testPutCategoryOnSpecifiedStore()
    {
        /** @var $store Mage_Core_Model_Store */
        $store = $this->getFixture('store');
        $categoryData = require TEST_FIXTURE_DIR . '/_data/Catalog/Category/category_store_data.php';
        /** @var $category Mage_Catalog_Model_Category */
        $category = $this->getFixture('category');
        $categoryDataInDefaultStore = array_intersect_key($category->getData(), $categoryData);
        $this->_updateCategoryWithPut($category->getId(), $categoryData, $store->getId());
        /** @var $updatedCategory Mage_Catalog_Model_Category */
        $updatedCategory = Mage::getModel('Mage_Catalog_Model_Category');
        $updatedCategory->setStoreId($store->getId())->load($category->getId());
        $expectedData = array_merge($categoryData, array('store_id' => $store->getId()));
        $this->_checkCategoryData($updatedCategory->getData(), $expectedData);

        // check that data was not set in default store
        /** @var $categoryInDefaultStore Mage_Catalog_Model_Category */
        $categoryInDefaultStore = Mage::getModel('Mage_Catalog_Model_Category')->load($updatedCategory->getId());
        $categoryDataInDefaultStore['available_sort_by'] = explode(',',
            $categoryDataInDefaultStore['available_sort_by']);
        $this->_checkCategoryData($categoryInDefaultStore->getData(), $categoryDataInDefaultStore);
    }

    /**
     * Test unsuccessful tree root category update
     *
     * @resourceOperation category::update
     */
    public function testPutTreeRootCategory()
    {
        $category = Mage::getModel('Mage_Catalog_Model_Category')->load(Mage_Catalog_Model_Category::TREE_ROOT_ID);
        $categoryData = require TEST_FIXTURE_DIR . '/_data/Catalog/Category/category_store_data.php';
        // any valid category should be set as parent one so as not to rise error related to 'parent_id' before ID check
        $categoryData['parent_id'] = $category->getParentId();
        $restResponse = $this->callPut($this->_getResourcePath($category->getId()), $categoryData);

        $expectedErrorMessage = "The tree root category cannot be changed.";
        $this->_checkErrorMessagesInResponse($restResponse, $expectedErrorMessage);
    }

    /**
     * Try to set parent_id field to be equal to id
     *
     * @magentoDataFixture fixture/Catalog/Category/category.php
     * @resourceOperation category::update
     */
    public function testPutParentIdEqualsId()
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = $this->getFixture('category');
        $categoryData = require TEST_FIXTURE_DIR . '/_data/Catalog/Category/category_store_data.php';
        $categoryData['parent_id'] = $category->getId();

        $restResponse = $this->callPut($this->_getResourcePath($category->getId()), $categoryData);
        $expectedErrorMessage = "Category 'parent_id' value cannot be equal to its 'id' value.";
        $this->_checkErrorMessagesInResponse($restResponse, $expectedErrorMessage);
    }

    /**
     * Try to move non-root category to become a root one
     *
     * @magentoDataFixture fixture/Catalog/Category/category.php
     * @resourceOperation category::update
     */
    public function testPutChangeParentToRootTree()
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = $this->getFixture('category');
        $categoryData = require TEST_FIXTURE_DIR . '/_data/Catalog/Category/category_store_data.php';
        $categoryData['parent_id'] = Mage_Catalog_Model_Category::TREE_ROOT_ID;

        $restResponse = $this->callPut($this->_getResourcePath($category->getId()), $categoryData);
        $expectedErrorMessage = "Non-root category cannot be updated to become a root one.";
        $this->_checkErrorMessagesInResponse($restResponse, $expectedErrorMessage);
    }

    /**
     * Test root category conversion into non-root one
     *
     * @magentoDataFixture fixture/Catalog/Category/root_category.php
     * @resourceOperation category::update
     */
    public function testPutConvertCategoryFromRoot()
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = Mage::getModel('Mage_Catalog_Model_Category')->load($this->getFixture('root_category')->getId());
        $defaultRootId = Mage::app()->getDefaultStoreView()->getRootCategoryId();
        $categoryData = array('parent_id' => $defaultRootId);
        $this->_updateCategoryWithPut($category->getId(), $categoryData);

        /** @var $categoryAfterUpdate Mage_Catalog_Model_Category */
        $categoryAfterUpdate = Mage::getModel('Mage_Catalog_Model_Category')->load($category->getId());
        $expectedCategroyData = array_merge($category->getData(), $categoryData, array(
            'path' => "1/$defaultRootId/{$category->getId()}", 'level' => 2));
        // value of 'updated_at' and 'position' cannot be predicted
        unset($expectedCategroyData['updated_at']);
        unset($expectedCategroyData['position']);
        $categoryAfterUpdateData = array_intersect_key($categoryAfterUpdate->getData(), $expectedCategroyData);
        $this->_checkCategoryData($categoryAfterUpdateData, $expectedCategroyData);
    }

    /**
     * Test category moving from one root to another
     *
     * @magentoDataFixture fixture/Catalog/Category/root_category.php
     * @magentoDataFixture fixture/Catalog/Category/category.php
     * @resourceOperation category::update
     */
    public function testPutMoveCategoryToAnotherRoot()
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = Mage::getModel('Mage_Catalog_Model_Category')->load($this->getFixture('category')->getId());
        $nonDefaultRootId = $this->getFixture('root_category')->getId();
        $categoryData = array('parent_id' => $nonDefaultRootId);
        $this->_updateCategoryWithPut($category->getId(), $categoryData);

        /** @var $categoryAfterUpdate Mage_Catalog_Model_Category */
        $categoryAfterUpdate = Mage::getModel('Mage_Catalog_Model_Category')->load($category->getId());
        $expectedCategroyData = array_merge($category->getData(), $categoryData, array(
            'path' => "1/$nonDefaultRootId/{$category->getId()}"));
        // value of 'updated_at' cannot be predicted
        unset($expectedCategroyData['updated_at']);
        // value of 'position' can vary across different test runs
        unset($expectedCategroyData['position']);
        $categoryAfterUpdateData = array_intersect_key($categoryAfterUpdate->getData(), $expectedCategroyData);
        $this->_checkCategoryData($categoryAfterUpdateData, $expectedCategroyData);
    }

    /**
     * Test categories tree fragment moving
     *
     * @magentoDataFixture fixture/Catalog/Category/category.php
     * @magentoDataFixture fixture/Catalog/Category/category_tree.php
     * @resourceOperation category::update
     */
    public function testPutMoveCategoriesTreeFragment()
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = $this->getFixture('category');
        $categoryTree = $this->getFixture('category_tree');
        /** @var $treeFragmentRoot Mage_Catalog_Model_Category */
        $treeFragmentRoot = $categoryTree[1];
        $categoryData = array('parent_id' => $category->getId());
        $this->_updateCategoryWithPut($treeFragmentRoot->getId(), $categoryData);

        // check if all moved categories were properly updated
        foreach ($categoryTree as &$categoryItem) {
            $categoryItem = Mage::getModel('Mage_Catalog_Model_Category')->load($categoryItem->getId());
        }
        $treeFragmentRoot = $categoryTree[1];
        $expectedTreeFragmentRootPath = "{$category->getPath()}/{$treeFragmentRoot->getId()}";
        $this->assertEquals($expectedTreeFragmentRootPath, $treeFragmentRoot->getPath(),
            "Tree fragment root was not moved correctly");
        /** @var $fragmentSubcategory Mage_Catalog_Model_Category */
        $fragmentSubcategory = $categoryTree[2];
        $this->assertEquals("$expectedTreeFragmentRootPath/{$fragmentSubcategory->getId()}",
            $fragmentSubcategory->getPath(), "Tree fragment subcategory was not moved correctly");
    }

    /**
     * Test moving category to one of its children
     *
     * @magentoDataFixture fixture/Catalog/Category/category_tree.php
     * @resourceOperation category::update
     */
    public function testPutMoveCategoryToChild()
    {
        $categoryTree = $this->getFixture('category_tree');
        /** @var $category Mage_Catalog_Model_Category */
        $category = $categoryTree[0];
        $childCategory = $categoryTree[2];
        $categoryData = array('parent_id' => $childCategory->getId());
        $restResponse = $this->callPut($this->_getResourcePath($category->getId()), $categoryData);
        $expectedErrorMessage = "The category cannot be moved under one of its child categories.";
        $this->_checkErrorMessagesInResponse($restResponse, $expectedErrorMessage);
    }

    /**
     * Test not existing category update
     *
     * @resourceOperation category::update
     */
    public function testPutNotExistingCategory()
    {
        $restResponse = $this->callPut($this->_getResourcePath('INVALID_ID'), array('parent_id' => 1));
        $expectedErrorMessage = "Resource not found.";
        $this->_checkErrorMessagesInResponse($restResponse, $expectedErrorMessage,
            Mage_Api2_Controller_Front_Rest::HTTP_NOT_FOUND);
    }

    /**
     * Test category without subcategories delete
     *
     * @magentoDataFixture fixture/Catalog/Category/category.php
     * @resourceOperation category::delete
     */
    public function testSimpleDelete()
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = $this->getFixture('category');
        $this->_deleteCategoryViaRest($category->getId());
    }

    /**
     * Test category without subcategories delete
     *
     * @magentoDataFixture fixture/Catalog/Category/category_tree.php
     * @resourceOperation category::delete
     */
    public function testDeleteWithSubcategories()
    {
        $categoryTree = $this->getFixture('category_tree');
        /** @var $subtreeRoot Mage_Catalog_Model_Category */
        $subtreeRoot = $categoryTree[0];
        $this->_deleteCategoryViaRest($subtreeRoot->getId());
        // check if subcategories are deleted
        /** @var $category Mage_Catalog_Model_Category */
        foreach ($categoryTree as $category) {
            if ($category->getId() != $subtreeRoot->getId()) {
                /** @var $subcategory Mage_Catalog_Model_Category */
                $subcategory = Mage::getModel('Mage_Catalog_Model_Category')->load($category->getId());
                $this->assertNull($subcategory->getId(), "Subcategory with ID = {$category->getId()} was not deleted.");
            }
        }
    }

    /**
     * Test unsucessful category tree root delete
     *
     * @resourceOperation category::delete
     */
    public function testDeleteCategoryTreeRoot()
    {
        $restResponse = $this->callDelete($this->_getResourcePath(Mage_Catalog_Model_Category::TREE_ROOT_ID));
        $expectedErrorMessage = "The tree root category cannot be deleted.";
        $this->_checkErrorMessagesInResponse($restResponse, $expectedErrorMessage);
    }

    /**
     * Test unsucessful category delete in case when it is root one and has store associated with it
     *
     * @resourceOperation category::delete
     */
    public function testDeleteRootCategoryWithAssociatedStore()
    {
        $categoryId = Mage::app()->getDefaultStoreView()->getRootCategoryId();
        $restResponse = $this->callDelete($this->_getResourcePath($categoryId));
        $expectedErrorMessage = "The root category cannot be deleted if there is a store associated with it.";
        $this->_checkErrorMessagesInResponse($restResponse, $expectedErrorMessage);
    }

    /**
     * Test not existing category delete
     *
     * @resourceOperation category::delete
     */
    public function testDeleteNotExistingCategory()
    {
        $restResponse = $this->callDelete($this->_getResourcePath('INVALID_ID'));
        $expectedErrorMessage = "Resource not found.";
        $this->_checkErrorMessagesInResponse($restResponse, $expectedErrorMessage,
            Mage_Api2_Controller_Front_Rest::HTTP_NOT_FOUND);
    }

    /**
     * Delete category using REST request. Check if it was really deleted
     *
     * @param $categoryId
     */
    protected function _deleteCategoryViaRest($categoryId)
    {
        $restResponse = $this->callDelete($this->_getResourcePath($categoryId));
        $this->assertEquals(Mage_Api2_Controller_Front_Rest::HTTP_OK, $restResponse->getStatus(), "Invalid response code");
        $deletedCategory = Mage::getModel('Mage_Catalog_Model_Category')->load($categoryId);
        $this->assertNull($deletedCategory->getId(), "Category was not deleted.");
    }

    /**
     * Check if category data equals to expected one
     *
     * @param array $realData
     * @param array $expectedData
     * @param array $nullFields Array of fields that are expected to be NULL in $category
     * @param array $inequalFields Array of fields that are expected to be inequal in $category and $categoryData
     */
    protected function _checkCategoryData($realData, $expectedData, $nullFields = array(), $inequalFields = array())
    {
        $dateAttributes = array('custom_design_from', 'custom_design_to');
        if (isset($expectedData['available_sort_by']) && is_string($expectedData['available_sort_by'])) {
            // 'available_sort_by' is represented as string in get and as array in put
            $expectedData['available_sort_by'] = explode(',', $expectedData['available_sort_by']);
        }
        // exclude from $expectedData all fields that must be null
        $expectedData = array_diff_key($expectedData, array_flip($nullFields));
        // check equal fields
        foreach ($expectedData as $key => $expectedValue) {
            $realValue = isset($realData[$key]) ? $realData[$key] : null;
            if (!in_array($key, $dateAttributes)) {
                if (!in_array($key, $inequalFields)) {
                    // check equal fields
                    $this->assertEquals($expectedValue, $realValue, "'$key' has invalid value.");
                } else {
                    // check inequal fields
                    $this->assertNotEquals($expectedValue, $realValue,
                        "'$key' has invalid value. Values are expected to be different.");
                }
            } else {
                if (!in_array($key, $inequalFields)) {
                    // check equal fields
                    $this->assertEquals(strtotime($expectedValue), strtotime($realValue), "'$key' has invalid value.");
                } else {
                    // check inequal fields
                    $this->assertNotEquals(strtotime($expectedValue), strtotime($realValue),
                        "'$key' has invalid value. Values are expected to be different.");
                }
            }
        }
        // check null fields
        foreach ($nullFields as $key => $expectedValue) {
            $realValue = isset($realData[$key]) ? $realData[$key] : null;
            $this->assertNull($realValue, "'$key' is expected to be NULL.");
        }
    }

    /**
     * Check if given category is expeced and count subcategories
     *
     * @param array $category category data from api response
     * @param array $categories expected categories
     * @return int
     */
    protected function _checkCategoryTree($category, $categories)
    {
        $subcategoriesCount = 0;
        $this->assertTrue(in_array($category['entity_id'], $categories), 'Not expected category in the tree');
        if (count($category['subcategories'])) {
            $subcategoriesCount++;
            foreach ($category['subcategories'] as $subcategory) {
                $subcategoriesCount += $this->_checkCategoryTree($subcategory, $categories);
            }
        }

        return $subcategoriesCount;
    }

    /**
     * Provider of valid data for category post test
     *
     * @dataSetNumber 3
     * @return array
     */
    public function dataProviderOfValidDataWithImages()
    {
        $imageFilePath = TEST_FIXTURE_DIR . '/_data/Catalog/Category/image.png';

        $imageAttributes = array('image', 'thumbnail');
        $imagesWithUniqueNames = array();
        foreach ($imageAttributes as $imageAttribute) {
            $imagesWithUniqueNames[$imageAttribute] = array(
                'file_name' => 'image' . uniqid(),
                'file_content' => base64_encode(file_get_contents($imageFilePath)),
                'file_mime_type' => 'image/png',
                'file_name_is_expected_to_be_changed' => false
            );
        }

        $imagesWithEmptyName = array();
        foreach ($imageAttributes as $imageAttribute) {
            $imagesWithEmptyName[$imageAttribute] = array(
                'file_name' => '',
                'file_content' => base64_encode(file_get_contents($imageFilePath)),
                'file_mime_type' => 'image/png',
                'file_name_is_expected_to_be_changed' => true
            );
        }

        $imagesWithTheEqualCustomNames = array();
        $uniqueName = 'image' . uniqid();
        $imagesWithTheEqualCustomNames['image'] = array(
            'file_name' => $uniqueName,
            'file_content' => base64_encode(file_get_contents($imageFilePath)),
            'file_mime_type' => 'image/png',
            'file_name_is_expected_to_be_changed' => false
        );
        $imagesWithTheEqualCustomNames['thumbnail'] = array(
            'file_name' => $uniqueName,
            'file_content' => base64_encode(file_get_contents($imageFilePath)),
            'file_mime_type' => 'image/png',
            'file_name_is_expected_to_be_changed' => true
        );

        $testImages = array(
            array($imagesWithUniqueNames),
            array($imagesWithEmptyName),
            array($imagesWithTheEqualCustomNames),
        );
        return $testImages;
    }

    /**
     * Provider of valid data for category post test
     *
     * @dataSetNumber 6
     * @return array
     */
    public function dataProviderOfValidData()
    {
        $simpleData = $this->_getValidCategoryData();
        $longValuesShouldBeCut = array_merge($this->_getValidCategoryData(), array(
            'name' => str_pad('', 500, 'long_name-'),
            'meta_title' => str_pad('', 500, 'long_meta_title-'),
            'url_key' => str_pad('', 500, 'long_url_key-'),
        ));
        $longValuesForTextAreas = array_merge($this->_getValidCategoryData(), array(
            'description' => str_pad('', 500, 'long_description-'),
            'meta_description' => str_pad('', 500, 'long_meta_description-'),
            'meta_keywords' => str_pad('', 500, 'long_meta_keywords-'),
        ));
        $zeroValuesAsInt = array_merge($this->_getValidCategoryData(), array(
            'is_active' => 0,
            'include_in_menu' => 0,
            'is_anchor' => 0,
            'custom_apply_to_products' => 0,
            'custom_use_parent_settings' => 0,
        ));
        $zeroValuesAsString = array_merge($this->_getValidCategoryData(), array(
            'is_active' => '0',
            'include_in_menu' => '0',
            'is_anchor' => '0',
            'custom_apply_to_products' => '0',
            'custom_use_parent_settings' => '0',
        ));
        $emptyDropDowns = array_merge($this->_getValidCategoryData(), array(
            'landing_page' => '',
            'custom_design' => '',
            'page_layout' => '',
        ));
        return array(
            array($simpleData),
            array($longValuesShouldBeCut, false),
            array($longValuesForTextAreas),
            array($zeroValuesAsInt),
            array($zeroValuesAsString),
            array($emptyDropDowns),
        );
    }

    /**
     * Provider of data with different 'use config' optioins configuration
     *
     * @dataSetNumber 3
     * @return array
     */
    public function dataProviderForPostWithUseConfig()
    {
        $categoryData = $this->_getValidCategoryData();
        $dataWithUseConfig = array_merge($categoryData, array(
            'use_config_filter_price_range' => 1, 'use_config_default_sort_by' => 1));
        $dataWithUseConfigWithoutValues = array_merge($dataWithUseConfig,
            array('filter_price_range' => null, 'default_sort_by' => null));
        $dataWithoutUseConfig = array_merge($categoryData, array(
            'use_config_filter_price_range' => 0, 'use_config_default_sort_by' => 0));
        return array(
            array($dataWithUseConfig, array('filter_price_range' => null, 'default_sort_by' => null)),
            array($dataWithUseConfigWithoutValues, array('filter_price_range' => null, 'default_sort_by' => null)),
            array($dataWithoutUseConfig, array('filter_price_range' => $dataWithoutUseConfig['filter_price_range'],
                'default_sort_by' => $dataWithoutUseConfig['default_sort_by'])));
    }

    /**
     * Create data for negative PUT and POST tests
     *
     * @return array
     */
    protected function _getInvalidDataForPutAndPost()
    {
        $emptyRequest = array('data' => array(), 'messages' => array('The request data is invalid.'));
        $invalidParentId = array('data' => array('parent_id' => 0),
            'messages' => array("Requested category does not exist."));
        $allFieldsInvalid = array(
            'data' => array_merge($this->_getValidCategoryData(), array(
                'name' => ' ',
                'filter_price_range' => 'INVALID_PRICE_RANGE',
                'custom_design_from' => '12-12-2012',
                'custom_design_to' => '2013/02/29',
                'default_sort_by' => 'INVALID_SORT',
                'is_active' => -1,
                'include_in_menu' => -1,
                'display_mode' => 'INVALID_DISPLAY_MODE',
                'landing_page' => -1,
                'is_anchor' => -1,
                'available_sort_by' => array('name', 'INVALID_SORT_BY'),
                'custom_design' => 'INVALID/INVALID',
                'page_layout' => 'INVALID_PAGE_LAYOUT',
            )),
            'message_patterns' => array(
                array('pattern' => '/Invalid value ".*" provided for ".+" attribute/', 'matches_count' => 9),
                array('pattern' => '/Date value for the ".+" attribute is invalid or has invalid format\. '
                    . 'Please use the following format: "yyyy-MM-dd"/', 'matches_count' => 2),
                array('pattern' => '/"\w+" attribute must have numeric positive value. "\w+" given/',
                    'matches_count' => 1),
                array('pattern' => '/"\w+" attribute cannot contain invisible characters only./', 'matches_count' => 1),
            )
        );
        $invalidDropDownValues['below_zero'] = array(
            'data' => array_merge($this->_getValidCategoryData(), array(
                'is_active' => -1,
                'include_in_menu' => -1,
                'display_mode' => -1,
                'available_sort_by' => array('name', -1),
                'default_sort_by' => -1,
                'is_anchor' => -1,
                'landing_page' => -1,
                'custom_design' => -1,
                'page_layout' => -1,
                'custom_use_parent_settings' => -1,
                'custom_apply_to_products' => -1,
            )),
            'message_patterns' => array(
                array('pattern' => '/Invalid value ".*" provided for ".+" attribute/', 'matches_count' => 11)
            )
        );
        $invalidDropDownValues['empty'] = array(
            'data' => array_merge($this->_getValidCategoryData(), array(
                'is_active' => '',
                'include_in_menu' => '',
                'available_sort_by' => array(),
                'default_sort_by' => '',
                'display_mode' => '',
                'is_anchor' => '',
                'custom_use_parent_settings' => '',
                'custom_apply_to_products' => '',
            )),
            'message_patterns' => array(
                array('pattern' => '/Invalid value "" provided for ".+" attribute/', 'matches_count' => 4),
                array('pattern' => '/Attribute ".+" is required/', 'matches_count' => 4),
            )
        );
        $invalidDropDownValues['out_of_range'] = array(
            'data' => array_merge($this->_getValidCategoryData(), array(
                'is_active' => 2,
                'include_in_menu' => 2,
                'display_mode' => 'invalid',
                'is_anchor' => 2,
                'available_sort_by' => array('name', 'invalid'),
                'default_sort_by' => 'invalid',
                'custom_use_parent_settings' => 2,
                'custom_apply_to_products' => 2,
                'custom_design' => 'invalid',
                'page_layout' => 'invalid',
                'landing_page' => 0,
            )),
            'message_patterns' => array(
                array('pattern' => '/Invalid value ".+" provided for ".+" attribute/', 'matches_count' => 11)
            )
        );
        $imageFilePath = TEST_FIXTURE_DIR . '/_data/Catalog/Category/image.png';
        $invalidImages['invalid_mime_type'] = array(
            'data' => array_merge($this->_getValidCategoryData(), array(
                'thumbnail' => array(
                    'file_name' => 'image' . uniqid(),
                    'file_content' => base64_encode(file_get_contents($imageFilePath)),
                    'file_mime_type' => 'image/bmp',
                ))),
            'messages' => array('Invalid value given for "thumbnail" attribute: "Unsuppoted file MIME type"')
        );
        $invalidImages['empty_mime_type'] = array(
            'data' => array_merge($this->_getValidCategoryData(), array(
                'thumbnail' => array(
                    'file_name' => 'image' . uniqid(),
                    'file_content' => base64_encode(file_get_contents($imageFilePath)),
                    'file_mime_type' => '',
                ))),
            'messages' => array('Invalid value given for "thumbnail" attribute: "\'file_mime_type\' is not specified."')
        );
        $invalidImages['empty_content'] = array(
            'data' => array_merge($this->_getValidCategoryData(), array(
                'thumbnail' => array(
                    'file_name' => 'image' . uniqid(),
                    'file_content' => '',
                    'file_mime_type' => 'image/jpg',
                ))),
            'messages' => array('Invalid value given for "thumbnail" attribute: "\'file_content\' is not specified."')
        );
        $invalidImages['invalid_content'] = array(
            'data' => array_merge($this->_getValidCategoryData(), array(
                'thumbnail' => array(
                    'file_name' => 'image' . uniqid(),
                    'file_content' => 'INVALID_CONTENT',
                    'file_mime_type' => 'image/jpg',
                ))),
            'messages' => array('Invalid value given for "thumbnail" attribute: "File content must be base64 encoded."')
        );

        $data = array(
            array($emptyRequest),
            array($invalidParentId),
            array($allFieldsInvalid),
        );
        foreach ($invalidDropDownValues as $invalidDropDownDataItem) {
            $data[] = array($invalidDropDownDataItem);
        }
        foreach ($invalidImages as $invalidImage) {
            $data[] = array($invalidImage);
        }
        return $data;
    }

    /**
     * Provide invalid data sets for category post
     * (dataSet number depends of what $this->_getInvalidDataForPutAndPost() returns)
     *
     * @dataSetNumber 13
     * @return array
     */
    public function dataProviderForPostInvalidData()
    {
        $emptyParentId = array('data' => array('description' => 'Test Description'),
            'messages' => array("'parent_id' attribute must be set in request."));
        $emptyRequiredFields = array(
            'data' => array('parent_id' => 2),
            'message_patterns' => array(
                array(
                    'pattern' => '/Attribute "\w+" is required/',
                    'matches_count' => 5,
                )
            )
        );
        $invalidUsageOfCustomDesignUseParentSettingsOption = array(
            'data' => array_merge($this->_getValidCategoryData(), array(
                'parent_id' => Mage_Catalog_Model_Category::TREE_ROOT_ID,
                'custom_use_parent_settings' => 1,
            )),
            'messages' => array("Custom design option 'custom_use_parent_settings' " .
                "cannot be used for root categories."));
        $data = array(
            array($emptyParentId),
            array($invalidUsageOfCustomDesignUseParentSettingsOption),
            array($emptyRequiredFields),
        );
        $data = array_merge($data, $this->_getInvalidDataForPutAndPost());
        return $data;
    }

    /**
     * Provide invalid data sets for category post
     * (dataSet number depends of what $this->_getInvalidDataForPutAndPost() returns)
     *
     * @dataSetNumber 10
     * @return array
     */
    public function dataProviderForPutInvalidData()
    {
        return $this->_getInvalidDataForPutAndPost();
    }

    /**
     * Retrieve created category id from rest response
     *
     * @param Magento_Test_Webservice_Rest_ResponseDecorator $restResponse
     * @return int
     */
    protected function _getCreatedCategoryId($restResponse)
    {
        $location = $restResponse->getHeader('Location');
        list($categoryId) = array_reverse(explode('/', $location));
        return $categoryId;
    }

    /**
     * Create category using POST request. Check if it was created
     *
     * @param array $categoryData
     * @param string $storeId
     * @return Mage_Catalog_Model_Category
     */
    protected function _createCategoryWithPost($categoryData, $storeId = null)
    {
        $restResponse = $this->callPost($this->_getResourcePath(null, $storeId), $categoryData);
        $this->assertEquals(Mage_Api2_Controller_Front_Rest::HTTP_OK, $restResponse->getStatus(), "Invalid response code");
        /** @var $createdCategory Mage_Catalog_Model_Category */
        $createdCategory = Mage::getModel('Mage_Catalog_Model_Category');
        if (!is_null($storeId)) {
            $createdCategory->setStoreId($storeId);
        }
        $createdCategory->load($this->_getCreatedCategoryId($restResponse));
        $this->assertNotEmpty($createdCategory->getId(), "Created category could not be loaded");
        $this->addModelToDelete($createdCategory, true);
        return $createdCategory;
    }

    /**
     * Update category using PUT request. Check if it was created
     *
     * @param int $categoryId
     * @param array $categoryData
     * @param string $storeId
     * @return Mage_Catalog_Model_Category
     */
    protected function _updateCategoryWithPut($categoryId, $categoryData, $storeId = null)
    {
        $restResponse = $this->callPut($this->_getResourcePath($categoryId, $storeId), $categoryData);
        $this->assertEquals(Mage_Api2_Controller_Front_Rest::HTTP_OK, $restResponse->getStatus(), "Invalid response code");
    }

    /**
     * Check if url is accessible with cURL
     *
     * @param string $url
     * @param string $urlField
     * @param int $expectedResponseCode
     */
    protected function _testUrlWithCurl($url, $urlField, $expectedResponseCode = 200)
    {
        $channel = curl_init();
        curl_setopt($channel, CURLOPT_URL, $url);
        curl_setopt($channel, CURLOPT_NOBODY, true);
        curl_exec($channel);
        $responseCode = curl_getinfo($channel, CURLINFO_HTTP_CODE);
        $this->assertEquals($expectedResponseCode, $responseCode, "'$urlField' is not accessible with cURL");
    }

    /**
     * Prepare valid category data
     *
     * @return array
     */
    protected function _getValidCategoryData()
    {
        $categoryFixturePath = $this->_getGlobalFixtureDirectory() . "/_block/Catalog/Category.php";
        $category = require $categoryFixturePath;
        $categoryData = $category->getData();
        unset($categoryData['path']);
        $defaultRootCategoryId = 2;
        $parentCategory = Mage::getModel('Mage_Catalog_Model_Category')->load($defaultRootCategoryId);
        $categoryData['parent_id'] = $parentCategory->getId();
        $categoryData['filter_price_range'] = '333.4444';
        return $categoryData;
    }

    /**
     * Retrieve directory with global fixtures
     *
     * @return string
     */
    protected function _getGlobalFixtureDirectory()
    {
        return realpath(dirname(__FILE__) . "/../../../../fixture");
    }

    /**
     * Create path to resource
     *
     * @param string $id
     * @param string $storeId
     * @return string
     */
    protected function _getResourcePath($id = null, $storeId = null)
    {
        $path = "categories";
        if (!is_null($id)) {
            $path .= "/$id";
        }
        if (!is_null($storeId)) {
            $path .= "/store/$storeId";
        }
        return $path;
    }
}
