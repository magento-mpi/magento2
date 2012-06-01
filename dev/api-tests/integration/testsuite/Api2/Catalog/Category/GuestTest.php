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
 * Test category resource for guest
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api2_Catalog_Category_GuestTest extends Magento_Test_Webservice_Rest_Guest
{
    /**
     * Delete fixtures
     */
    protected function tearDown()
    {
        self::deleteFixture('category', true);
        self::deleteFixture('category_disabled', true);
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
     * Test that category creation is forbidden for customer
     *
     * @resourceOperation category::create
     */
    public function testPost()
    {
        $restResponse = $this->callPost($this->_getResourcePath(), array());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_FORBIDDEN, $restResponse->getStatus());
    }

    /**
     * Test that category update is forbidden for customer
     *
     * @resourceOperation category::update
     */
    public function testPut()
    {
        $restResponse = $this->callPut($this->_getResourcePath(Mage_Catalog_Model_Category::TREE_ROOT_ID), array());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_FORBIDDEN, $restResponse->getStatus());
    }

    /**
     * Test that category delete is forbidden for customer
     *
     * @resourceOperation category::delete
     */
    public function testDelete()
    {
        $restResponse = $this->callDelete($this->_getResourcePath(Mage_Catalog_Model_Category::TREE_ROOT_ID), array());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_FORBIDDEN, $restResponse->getStatus());
    }

    /**
     * Test successful category get
     *
     * @magentoDataFixture Catalog/Category/category.php
     * @resourceOperation category::get
     */
    public function testGet()
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = $this->getFixture('category');
        $restResponse = $this->callGet($this->_getResourcePath($category->getId()));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);
        $originalData = $category->getData();
        $originalData['url_path'] = $originalData['url_key'] . '.html';
        foreach ($responseData as $field => $value) {
            if (!is_array($value)) {
                $this->assertEquals($originalData[$field], $value, "{$field} is invalid.");
            }
        }
    }

    /**
     * Test category get when requested category is disabled
     *
     * @magentoDataFixture Catalog/Category/category_disabled.php
     * @resourceOperation category::get
     */
    public function testGetCategoryDisabled()
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = $this->getFixture('category_disabled');

        $restResponse = $this->callGet($this->_getResourcePath($category->getId()));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }

    /**
     * Test category get when requested category is not assigned to requested store
     *
     * @magentoDataFixture Catalog/Category/category_on_new_website.php
     * @resourceOperation category::get
     */
    public function testGetCategoryNotAssignedToStore()
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = $this->getFixture('category_on_new_website');
        $storeId = Mage::app()->getDefaultStoreView()->getId();
        // we are requesting category on the store that it's not assigned to
        $restResponse = $this->callGet($this->_getResourcePath($category->getId(), $storeId));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }

    /**
     * Test successful category tree get
     *
     * @magentoDataFixture Catalog/Category/category_on_new_website.php
     * @resourceOperation category::multiget
     */
    public function testGetCategoriesTree()
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = $this->getFixture('category_on_new_website');
        /** @var $storeGroup Mage_Core_Model_Store_Group */
        $storeGroup = $this->getFixture('store_group');
        $storeId = reset($storeGroup->getStoreIds());
        $restResponse = $this->callGet($this->_getResourcePath(null, $storeId));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);
        $treeRootCategory = reset($responseData);
        // Assert that category which we created and set as default for store group is returned as tree root
        $this->assertEquals($treeRootCategory['entity_id'], $category->getId());
    }

    /**
     * Test successful category tree get with root specified
     *
     * @magentoDataFixture Catalog/Category/category_tree.php
     * @resourceOperation category::multiget
     */
    public function testGetCategoriesTreeWithRoot()
    {
        $categoryTree = $this->getFixture('category_tree');
        /** @var $parentCategory Mage_Catalog_Model_Category */
        $parentCategory = reset($categoryTree);
        $restResponse = $this->callGet($this->_getResourcePath(), array('root' => $parentCategory->getId()));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);

        $responseParentCategory = reset($responseData);
        $expectedCategories = array();
        foreach ($categoryTree as $category) {
            $expectedCategories[] = $category->getId();
        }

        $subcategoriesCount = $this->_checkCategoryTree($responseParentCategory, $expectedCategories);
        $this->assertEquals($subcategoriesCount, count($categoryTree) - 1);
    }

    /**
     * Test category tree get when requested root category is disabled
     *
     * @magentoDataFixture Catalog/Category/category_disabled.php
     * @resourceOperation category::multiget
     */
    public function testGetCategoriesTreeRootDisabled()
    {
        /** @var $categoryRoot Mage_Catalog_Model_Category */
        $categoryRoot = $this->getFixture('category_disabled');

        $restResponse = $this->callGet($this->_getResourcePath(), array('root' => $categoryRoot->getId()));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }

    /**
     * Test category tree get when requested root category is not assigned to requested store
     *
     * @magentoDataFixture Catalog/Category/category_on_new_website.php
     * @resourceOperation category::multiget
     */
    public function testGetCategoriesTreeRootNotAssignedToStore()
    {
        /** @var $categoryRoot Mage_Catalog_Model_Category */
        $categoryRoot = $this->getFixture('category_on_new_website');
        $storeId = Mage::app()->getDefaultStoreView()->getId();
        // we are requesting category tree on the store that it's not assigned to
        $restResponse = $this->callGet($this->_getResourcePath(null, $storeId),
            array('root' => $categoryRoot->getId()));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
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
