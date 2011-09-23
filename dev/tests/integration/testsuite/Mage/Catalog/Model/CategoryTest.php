<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Catalog_Model_Category.
 *
 * @group module:Mage_Catalog
 * @magentoDataFixture Mage/Catalog/_files/categories.php
 */
class Mage_Catalog_Model_CategoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Catalog_Model_Category
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Mage_Catalog_Model_Category();
    }

    public function testGetUrlInstance()
    {
        $instance = $this->_model->getUrlInstance();
        $this->assertInstanceOf('Mage_Core_Model_Url', $instance);
        $this->assertSame($instance, $this->_model->getUrlInstance());
    }

    public function testGetUrlRewrite()
    {
        $rewrite = $this->_model->getUrlRewrite();
        $this->assertInstanceOf('Mage_Core_Model_Url_Rewrite', $rewrite);
        $this->assertSame($rewrite, $this->_model->getUrlRewrite());
    }

    public function testGetTreeModel()
    {
        $model = $this->_model->getTreeModel();
        $this->assertInstanceOf('Mage_Catalog_Model_Resource_Category_Tree', $model);
        $this->assertNotSame($model, $this->_model->getTreeModel());
    }

    public function testGetTreeModelInstance()
    {
        $model = $this->_model->getTreeModelInstance();
        $this->assertInstanceOf('Mage_Catalog_Model_Resource_Category_Tree', $model);
        $this->assertSame($model, $this->_model->getTreeModelInstance());
    }

    public function testMove()
    {
        $this->_model->load(7);
        $this->assertEquals(2, $this->_model->getParentId());
        $this->_model->move(6, 0);
        /* load is not enough to reset category data */
        $this->_model->setData(array());
        $this->_model->load(7);
        $this->assertEquals(6, $this->_model->getParentId());
    }

    /**
     * @expectedException Mage_Core_Exception
     */
    public function testMoveWrongParent()
    {
        $this->_model->load(7);
        $this->_model->move(100, 0);
    }

    /**
     * @expectedException Mage_Core_Exception
     */
    public function testMoveWrongId()
    {
        $this->_model->move(100, 0);
    }

    public function testGetDefaultAttributeSetId()
    {
        /* based on value installed in DB */
        $this->assertEquals(3, $this->_model->getDefaultAttributeSetId());
    }

    public function testGetProductCollection()
    {
        $collection = $this->_model->getProductCollection();
        $this->assertInstanceOf('Mage_Catalog_Model_Resource_Product_Collection', $collection);
        $this->assertEquals($this->_model->getStoreId(), $collection->getStoreId());
    }

    public function testGetAttributes()
    {
        $attributes = $this->_model->getAttributes();
        $this->assertArrayHasKey('name', $attributes);
        $this->assertArrayHasKey('custom_design', $attributes);

        $attributes = $this->_model->getAttributes(true);
        $this->assertArrayHasKey('name', $attributes);
        $this->assertArrayNotHasKey('custom_design', $attributes);
    }

    public function testGetProductsPosition()
    {
        $this->assertEquals(array(), $this->_model->getProductsPosition());
        $this->_model->unsetData();
        $this->_model->load(6);
        $this->assertEquals(array(), $this->_model->getProductsPosition());

        $this->_model->unsetData();
        $this->_model->load(4);
        $this->assertContains(1, $this->_model->getProductsPosition());
    }

    public function testGetStoreIds()
    {
        $this->_model->load(3); /* id from fixture */
        $this->assertContains(Mage::app()->getStore()->getId(), $this->_model->getStoreIds());
    }

    public function testGetLayoutUpdateHandle()
    {
        $this->assertEquals('catalog_category_default', $this->_model->getLayoutUpdateHandle());
        $this->_model->setIsAnchor(true);
        $this->assertEquals('catalog_category_layered', $this->_model->getLayoutUpdateHandle());
    }

    public function testSetGetStoreId()
    {
        $this->assertEquals(Mage::app()->getStore()->getId(), $this->_model->getStoreId());
        $this->_model->setStoreId(1000);
        $this->assertEquals(1000, $this->_model->getStoreId());
    }

    public function testGetUrl()
    {
        $this->assertStringEndsWith('catalog/category/view/', $this->_model->getUrl());

        $this->_model->setUrl('test_url');
        $this->assertEquals('test_url', $this->_model->getUrl());

        $this->_model->setUrl(null);
        $this->_model->setRequestPath('test_path');
        $this->assertStringEndsWith('test_path', $this->_model->getUrl());

        $this->_model->setUrl(null);
        $this->_model->setRequestPath(null);
        $this->_model->setId(1000);
        $this->assertStringEndsWith('catalog/category/view/id/1000/', $this->_model->getUrl());
    }

    public function testGetCategoryIdUrl()
    {
        $this->assertStringEndsWith('catalog/category/view/', $this->_model->getCategoryIdUrl());
        $this->_model->setUrlKey('test_key');
        $this->assertStringEndsWith('catalog/category/view/s/test_key/', $this->_model->getCategoryIdUrl());
    }

    public function testFormatUrlKey()
    {
        $this->assertEquals('test', $this->_model->formatUrlKey('test'));
        $this->assertEquals('test-some-chars-5', $this->_model->formatUrlKey('test-some#-chars^5'));
        $this->assertEquals('test', $this->_model->formatUrlKey('test-????????'));
    }

    public function testGetImageUrl()
    {
        $this->assertFalse($this->_model->getImageUrl());
        $this->_model->setImage('test.gif');
        $this->assertStringEndsWith('media/catalog/category/test.gif', $this->_model->getImageUrl());
    }

    public function testGetUrlPath()
    {
        $this->assertNull($this->_model->getUrlPath());
        $this->_model->load(5);
        $this->assertEquals('category-1/category-1-1/category-1-1-1.html', $this->_model->getUrlPath());
    }

    public function testGetParentCategory()
    {
        $category = $this->_model->getParentCategory();
        $this->assertInstanceOf('Mage_Catalog_Model_Category', $category);
        $this->assertSame($category, $this->_model->getParentCategory());
    }

    public function testGetParentId()
    {
        $this->assertEquals(0, $this->_model->getParentId());
        $this->_model->unsetData();
        $this->_model->load(4);
        $this->assertEquals(3, $this->_model->getParentId());
    }

    public function testGetParentIds()
    {
        $this->assertEquals(array(), $this->_model->getParentIds());
        $this->_model->unsetData();
        $this->_model->load(4);
        $this->assertContains(3, $this->_model->getParentIds());
        $this->assertNotContains(4, $this->_model->getParentIds());
    }

    public function testGetCustomDesignDate()
    {
        $dates = $this->_model->getCustomDesignDate();
        $this->assertArrayHasKey('from', $dates);
        $this->assertArrayHasKey('to', $dates);
    }

    public function testGetDesignAttributes()
    {
        $attributes = $this->_model->getDesignAttributes();
        $this->assertContains('custom_design_from', array_keys($attributes));
        $this->assertContains('custom_design_to', array_keys($attributes));
    }

    public function testGetAllChildren()
    {
        $this->_model->load(4);
        $this->assertEquals('4,5', $this->_model->getAllChildren());
        $this->_model->load(5);
        $this->assertEquals('5', $this->_model->getAllChildren());
    }

    public function testGetChildren()
    {
        $this->_model->load(3);
        $this->assertEquals('4', $this->_model->getChildren());
    }

    public function testGetPathInStore()
    {
        $this->_model->load(5);
        $this->assertEquals('5,4,3', $this->_model->getPathInStore());
    }

    public function testCheckId()
    {
        $this->assertEquals(4, $this->_model->checkId(4));
        $this->assertFalse($this->_model->checkId(111));
    }

    public function testGetPathIds()
    {
        $this->assertEquals(array(''), $this->_model->getPathIds());
        $this->_model->setPathIds(array(1));
        $this->assertEquals(array(1), $this->_model->getPathIds());

        $this->_model->unsetData();
        $this->_model->setPath('1/2/3');
        $this->assertEquals(array(1,2,3), $this->_model->getPathIds());
    }

    public function testGetLevel()
    {
        $this->assertEquals(0, $this->_model->getLevel());
        $this->_model->setData('level', 1);
        $this->assertEquals(1, $this->_model->getLevel());

    }

    public function testVerifyIds()
    {
        $ids = $this->_model->verifyIds(array(1, 2, 3, 4, 100));
        $this->assertContains(4, $ids);
        $this->assertNotContains(100, $ids);
    }

    public function testHasChildren()
    {
        $this->_model->load(3);
        $this->assertTrue($this->_model->hasChildren());
        $this->_model->load(5);
        $this->assertFalse($this->_model->hasChildren());
    }

    public function testGetRequestPath()
    {
        $this->assertNull($this->_model->getRequestPath());
        $this->_model->setData('request_path', 'test');
        $this->assertEquals('test', $this->_model->getRequestPath());
    }

    public function testGetName()
    {
        $this->assertNull($this->_model->getName());
        $this->_model->setData('name', 'test');
        $this->assertEquals('test', $this->_model->getName());
    }

    public function testGetAnchorsAbove()
    {
        $this->_model->load(4);
        $this->assertEmpty($this->_model->getAnchorsAbove());
        $this->_model->load(5);
        $this->assertContains(4, $this->_model->getAnchorsAbove());
    }

    public function testGetProductCount()
    {
        $this->_model->load(6);
        $this->assertEquals(0, $this->_model->getProductCount());
        $this->_model->setData(array());
        $this->_model->load(3);
        $this->assertEquals(1, $this->_model->getProductCount());
    }

    public function testGetParentCategories()
    {
        $this->_model->load(5);
        $parents = $this->_model->getParentCategories();
        $this->assertEquals(3, count($parents));
    }

    public function testGetParentCategoriesEmpty()
    {
        $this->_model->load(1);
        $parents = $this->_model->getParentCategories();
        $this->assertEquals(0, count($parents));
    }


    public function testGetChildrenCategories()
    {
        $this->_model->load(3);
        $children = $this->_model->getChildrenCategories();
        $this->assertEquals(1, count($children));
    }

    public function testGetChildrenCategoriesEmpty()
    {
        $this->_model->load(5);
        $children = $this->_model->getChildrenCategories();
        $this->assertEquals(0, count($children));
    }

    public function testGetParentDesignCategory()
    {
        $this->_model->load(5);
        $parent = $this->_model->getParentDesignCategory();
        $this->assertEquals(5, $parent->getId());
    }

    public function testIsInRootCategoryList()
    {
        $this->assertFalse($this->_model->isInRootCategoryList());
        $this->_model->unsetData();
        $this->_model->load(3);
        $this->assertTrue($this->_model->isInRootCategoryList());
    }

    public function testGetAvailableSortBy()
    {
        $this->assertEquals(array(), $this->_model->getAvailableSortBy());
        $this->_model->setData('available_sort_by', 'test,and,test');
        $this->assertEquals(array('test', 'and', 'test'), $this->_model->getAvailableSortBy());
    }

    public function testGetAvailableSortByOptions()
    {
        $options = $this->_model->getAvailableSortByOptions();
        $this->assertContains('price', array_keys($options));
        $this->assertContains('position', array_keys($options));
        $this->assertContains('name', array_keys($options));
    }

    public function testGetDefaultSortBy()
    {
        $this->assertEquals('position', $this->_model->getDefaultSortBy());
    }

    public function testValidate()
    {
        $this->assertNotEmpty($this->_model->validate());
    }
}
