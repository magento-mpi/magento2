<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  compatibility_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test Category methods compatibility between previous and current API versions.
 */
class Compatibility_Soap_Catalog_Category_CategoryTest extends Compatibility_Soap_SoapAbstract
{
    /**
     * Category ID created at previous API
     * @var int
     */
    protected static $_prevCategoryId;

    /**
     * Category ID created at current API
     * @var int
     */
    protected static $_currCategoryId;

    /**
     * Product ID created at previous API
     * @var int
     */
    protected static $_prevProductId;

    /**
     * Product ID created at current API
     * @var int
     */
    protected static $_currProductId;

    /**
     * Test category current store method compatibility.
     * Scenario:
     * 1. Get category current store at previous API.
     * 2. Get category current store  at current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     */
    public function testCatalogCategoryCurrentStore()
    {
        $apiMethod = 'catalog_category.currentStore';
        $prevResponse = $this->prevCall($apiMethod);
        $currResponse = $this->currCall($apiMethod);
        $this->_checkVersionType($prevResponse, $currResponse, $apiMethod);
    }

    /**
     * Test category create method compatibility.
     * Scenario:
     * 1. Create category in previous API.
     * 2. Create category in current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     */
    public function testCatalogCategoryCreate()
    {
        $apiMethod = 'catalog_category.create';
        $categoryIds = $this->_createCategories();
        self::$_currCategoryId = $categoryIds['currCategoryId'];
        self::$_prevCategoryId = $categoryIds['prevCategoryId'];
        $this->_checkVersionType(self::$_prevCategoryId, self::$_currCategoryId, $apiMethod);
    }

    /**
     * Test category level method compatibility.
     * Scenario:
     * 1. Level category in previous API.
     * 2. Level categoty in current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     * @depends testCatalogCategoryCreate
     */
    public function testCatalogCategoryLevel()
    {
        $apiMethod = 'catalog_category.level';
        $prevResponse = $this->prevCall($apiMethod);
        $currResponse = $this->currCall($apiMethod);
        $this->_checkVersionType($prevResponse, $currResponse, $apiMethod);
    }

    /**
     * Test category info method compatibility.
     * Scenario:
     * 1. Retrieve category in previous API.
     * 2. Retrieve categoty in current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     * @depends testCatalogCategoryCreate
     */
    public function testCatalogCategoryInfo()
    {
        $apiMethod = 'catalog_category.info';
        $prevResponse = $this->prevCall($apiMethod, array('categoryId' => self::$_prevCategoryId));
        $currResponse = $this->currCall($apiMethod, array('categoryId' => self::$_currCategoryId));
        $this->_checkVersionType($prevResponse, $currResponse, $apiMethod);
    }

    /**
     * Test category tree method compatibility.
     * Scenario:
     * 1. Retrieve tree of categories in previous API.
     * 2. Retrieve tree of categories in current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     */
    public function testCatalogCategoryTree()
    {
        $apiMethod = 'catalog_category.tree';
        $prevResponse = $this->prevCall($apiMethod, array('parentId' => Mage_Catalog_Model_Category::TREE_ROOT_ID));
        $currResponse = $this->currCall($apiMethod, array('parentId' => Mage_Catalog_Model_Category::TREE_ROOT_ID));
        $this->_checkVersionType($prevResponse, $currResponse, $apiMethod);
    }

    /**
     * Test category update method compatibility.
     * Scenario:
     * 1. Update category in previous API.
     * 2. Update category in current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     * @depends testCatalogCategoryCreate
     */
    public function testCatalogCategoryUpdate()
    {
        $apiMethod = 'catalog_category.update';
        $updateCategoryData = array(
            'name' => 'Category ' . uniqid(),
            'is_active' => '0',
            'include_in_menu' => '0',
            'available_sort_by' => array('name', 'price'),
            'default_sort_by' => 'name');
        $prevResponse = $this->prevCall($apiMethod, array(
            'categoryId' => self::$_prevCategoryId,
            'categoryData' => $updateCategoryData));
        $currResponse = $this->currCall($apiMethod, array(
            'categoryId' => self::$_currCategoryId,
            'categoryData' => $updateCategoryData));
        $this->_checkVersionType($prevResponse, $currResponse, $apiMethod);
    }

    /**
     * Test category move method compatibility.
     * Scenario:
     * 1. Move category in previous API.
     * 2. Move categoty in current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     * @depends testCatalogCategoryCreate
     */
    public function testCatalogCategoryMove()
    {
        $apiMethod = 'catalog_category.move';
        $parentIds = $this->_createCategories();
        $prevResponse = $this->prevCall($apiMethod, array(
            'categoryId' => self::$_prevCategoryId,
            'parentId' => $parentIds['prevCategoryId'],
            'afterId' => Mage_Catalog_Model_Category::TREE_ROOT_ID));
        $currResponse = $this->currCall($apiMethod, array(
            'categoryId' => self::$_currCategoryId,
            'parentId' => $parentIds['currCategoryId'],
            'afterId' => Mage_Catalog_Model_Category::TREE_ROOT_ID));
        $this->_checkVersionType($prevResponse, $currResponse, $apiMethod);
    }

    /**
     * Test assign product to category method compatibility.
     * Scenario:
     * 1. Assign product to category in previous API.
     * 2. Assign product to category in current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     * @depends testCatalogCategoryCreate
     */
    public function testCatalogCategoryAssignProduct()
    {
        $apiMethod = 'catalog_category.assignProduct';
        $productIds = $this->_createProducts();
        self::$_currProductId = $productIds['currProductId'];
        self::$_prevProductId = $productIds['prevProductId'];
        $prevResponse = $this->prevCall($apiMethod, array(
            'categoryId' => self::$_prevCategoryId,
            'productId' => self::$_prevProductId));
        $currResponse = $this->currCall($apiMethod, array(
            'categoryId' => self::$_currCategoryId,
            'productId' => self::$_currProductId));
        $this->_checkVersionType($prevResponse, $currResponse, $apiMethod);
    }

    /**
     * Test assigned products method compatibility.
     * Scenario:
     * 1. Retrieve assigned products to category in previous API.
     * 2. Retrieve assigned products to category in current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     * @depends testCatalogCategoryCreate
     */
    public function testCatalogCategoryAssignedProducts()
    {
        $apiMethod = 'catalog_category.assignedProducts';
        $prevResponse = $this->prevCall($apiMethod, array(
            'categoryId' => self::$_prevCategoryId));
        $currResponse = $this->currCall($apiMethod, array(
            'categoryId' => self::$_currCategoryId));
        $this->_checkVersionType($prevResponse, $currResponse, $apiMethod);
    }

    /**
     * Test update assigned products method compatibility.
     * Scenario:
     * 1. Update assigned product in previous API.
     * 2. Update assigned product in current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     * @depends testCatalogCategoryCreate
     * @depends testCatalogCategoryAssignProduct
     */
    public function testCatalogCategoryUpdateProduct()
    {
        $apiMethod = 'catalog_category.updateProduct';
        $prevResponse = $this->prevCall($apiMethod, array(
            'categoryId' => self::$_prevCategoryId,
            'productId' => self::$_prevProductId,
            'position' => '10'
        ));
        $currResponse = $this->currCall($apiMethod, array(
            'categoryId' => self::$_currCategoryId,
            'productId' => self::$_currProductId,
            'position' => '10'
        ));
        $this->_checkVersionType($prevResponse, $currResponse, $apiMethod);
    }

    /**
     * Test remove assigned product method compatibility.
     * Scenario:
     * 1. Remove assigned product in previous API.
     * 2. remove assigned product in current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     * @depends testCatalogCategoryCreate
     * @depends testCatalogCategoryAssignProduct
     */
    public function testCatalogCategoryRemoveProduct()
    {
        $apiMethod = 'catalog_category.removeProduct';
        $prevResponse = $this->prevCall($apiMethod, array(
            'categoryId' => self::$_prevCategoryId,
            'productId' => self::$_prevProductId
        ));
        $currResponse = $this->currCall($apiMethod, array(
            'categoryId' => self::$_currCategoryId,
            'productId' => self::$_currProductId
        ));
        $this->_checkVersionType($prevResponse, $currResponse, $apiMethod);
    }

    /**
     * Test category delete method compatibility.
     * Scenario:
     * 1. Delete category in previous API.
     * 2. Delete category in current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     * @depends testCatalogCategoryCreate
     */
    public function testCatalogCategoryDelete()
    {
        $apiMethod = 'catalog_category.delete';
        $prevResponse = $this->prevCall($apiMethod, array('categoryId' => self::$_prevCategoryId));
        $currResponse = $this->currCall($apiMethod, array('categoryId' => self::$_currCategoryId));
        $this->_checkVersionType($prevResponse, $currResponse, $apiMethod);
    }
}
