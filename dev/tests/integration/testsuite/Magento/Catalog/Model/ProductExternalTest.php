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
 * Tests product model:
 * - external interaction is tested
 *
 * @see Magento_Catalog_Model_ProductTest
 * @see Magento_Catalog_Model_ProductPriceTest
 * @magentoDataFixture Magento/Catalog/_files/categories.php
 */
class Magento_Catalog_Model_ProductExternalTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Catalog_Model_Product
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Mage::getModel('Magento_Catalog_Model_Product');
    }

    public function testGetStoreId()
    {
        $this->assertEquals(Mage::app()->getStore()->getId(), $this->_model->getStoreId());
        $this->_model->setData('store_id', 999);
        $this->assertEquals(999, $this->_model->getStoreId());
    }

    public function testGetLinkInstance()
    {
        $model = $this->_model->getLinkInstance();
        $this->assertInstanceOf('Magento_Catalog_Model_Product_Link', $model);
        $this->assertSame($model, $this->_model->getLinkInstance());
    }

    public function testGetCategoryId()
    {
        $this->assertFalse($this->_model->getCategoryId());
        $category = new \Magento\Object(array('id' => 5));
        Mage::register('current_category', $category);
        try {
            $this->assertEquals(5, $this->_model->getCategoryId());
            Mage::unregister('current_category');
        } catch (Exception $e) {
            Mage::unregister('current_category');
            throw $e;
        }
    }

    public function testGetCategory()
    {
        $this->assertEmpty($this->_model->getCategory());

        Mage::register('current_category', new \Magento\Object(array('id' => 3))); // fixture
        try {
            $category = $this->_model->getCategory();
            $this->assertInstanceOf('Magento_Catalog_Model_Category', $category);
            $this->assertEquals(3, $category->getId());
            Mage::unregister('current_category');
        } catch (Exception $e) {
            Mage::unregister('current_category');
            throw $e;
        }

        $categoryTwo = new StdClass;
        $this->_model->setCategory($categoryTwo);
        $this->assertSame($categoryTwo, $this->_model->getCategory());
    }

    public function testGetCategoryIds()
    {
        // none
        /** @var $model Magento_Catalog_Model_Product */
        $model = Mage::getModel('Magento_Catalog_Model_Product');
        $this->assertEquals(array(), $model->getCategoryIds());

        // fixture
        $this->_model->setId(1);
        $this->assertEquals(array(2, 3, 4), $this->_model->getCategoryIds());
    }

    public function testGetCategoryCollection()
    {
        // empty
        $collection = $this->_model->getCategoryCollection();
        $this->assertInstanceOf('Magento_Catalog_Model_Resource_Category_Collection', $collection);

        // fixture
        $this->_model->setId(1);
        $fixtureCollection = $this->_model->getCategoryCollection();
        $this->assertInstanceOf('Magento_Catalog_Model_Resource_Category_Collection', $fixtureCollection);
        $this->assertNotSame($fixtureCollection, $collection);
        $ids = array();
        foreach ($fixtureCollection as $category) {
            $ids[] = $category->getId();
        }
        $this->assertEquals(array(2, 3, 4), $ids);
    }

    public function testGetWebsiteIds()
    {
        // set
        /** @var $model Magento_Catalog_Model_Product */
        $model = Mage::getModel('Magento_Catalog_Model_Product',
            array('data' => array('website_ids' => array(1, 2)))
        );
        $this->assertEquals(array(1, 2), $model->getWebsiteIds());

        // fixture
        $this->_model->setId(1);
        $this->assertEquals(array(1), $this->_model->getWebsiteIds());
    }

    public function testGetStoreIds()
    {
        // set
        /** @var $model Magento_Catalog_Model_Product */
        $model = Mage::getModel('Magento_Catalog_Model_Product',
            array('data' => array('store_ids' => array(1, 2)))
        );
        $this->assertEquals(array(1, 2), $model->getStoreIds());

        // fixture
        $this->_model->setId(1);
        $this->assertEquals(array(1), $this->_model->getStoreIds());
    }

    /**
     * @covers Magento_Catalog_Model_Product::getRelatedProducts
     * @covers Magento_Catalog_Model_Product::getRelatedProductIds
     * @covers Magento_Catalog_Model_Product::getRelatedProductCollection
     * @covers Magento_Catalog_Model_Product::getRelatedLinkCollection
     */
    public function testRelatedProductsApi()
    {
        $this->assertEquals(array(), $this->_model->getRelatedProducts());
        $this->assertEquals(array(), $this->_model->getRelatedProductIds());

        $collection = $this->_model->getRelatedProductCollection();
        $this->assertInstanceOf('Magento_Catalog_Model_Resource_Product_Collection', $collection);
        $this->assertSame($this->_model, $collection->getProduct());

        $linkCollection = $this->_model->getRelatedLinkCollection();
        $this->assertInstanceOf('Magento_Catalog_Model_Resource_Product_Link_Collection', $linkCollection);
        $this->assertSame($this->_model, $linkCollection->getProduct());
    }

    /**
     * @covers Magento_Catalog_Model_Product::getUpSellProducts
     * @covers Magento_Catalog_Model_Product::getUpSellProductIds
     * @covers Magento_Catalog_Model_Product::getUpSellProductCollection
     * @covers Magento_Catalog_Model_Product::getUpSellLinkCollection
     */
    public function testUpSellProductsApi()
    {
        $this->assertEquals(array(), $this->_model->getUpSellProducts());
        $this->assertEquals(array(), $this->_model->getUpSellProductIds());

        $collection = $this->_model->getUpSellProductCollection();
        $this->assertInstanceOf('Magento_Catalog_Model_Resource_Product_Collection', $collection);
        $this->assertSame($this->_model, $collection->getProduct());

        $linkCollection = $this->_model->getUpSellLinkCollection();
        $this->assertInstanceOf('Magento_Catalog_Model_Resource_Product_Link_Collection', $linkCollection);
        $this->assertSame($this->_model, $linkCollection->getProduct());
    }

    /**
     * @covers Magento_Catalog_Model_Product::getCrossSellProducts
     * @covers Magento_Catalog_Model_Product::getCrossSellProductIds
     * @covers Magento_Catalog_Model_Product::getCrossSellProductCollection
     * @covers Magento_Catalog_Model_Product::getCrossSellLinkCollection
     */
    public function testCrossSellProductsApi()
    {
        $this->assertEquals(array(), $this->_model->getCrossSellProducts());
        $this->assertEquals(array(), $this->_model->getCrossSellProductIds());

        $collection = $this->_model->getCrossSellProductCollection();
        $this->assertInstanceOf('Magento_Catalog_Model_Resource_Product_Collection', $collection);
        $this->assertSame($this->_model, $collection->getProduct());

        $linkCollection = $this->_model->getCrossSellLinkCollection();
        $this->assertInstanceOf('Magento_Catalog_Model_Resource_Product_Link_Collection', $linkCollection);
        $this->assertSame($this->_model, $linkCollection->getProduct());
    }

    public function testGetGroupedLinkCollection()
    {
        $linkCollection = $this->_model->getGroupedLinkCollection();
        $this->assertInstanceOf('Magento_Catalog_Model_Resource_Product_Link_Collection', $linkCollection);
        $this->assertSame($this->_model, $linkCollection->getProduct());
    }

    /**
     * @covers Magento_Catalog_Model_Product::getProductUrl
     * @covers Magento_Catalog_Model_Product::getUrlInStore
     */
    public function testGetProductUrl()
    {
        $this->assertStringEndsWith('catalog/product/view/', $this->_model->getProductUrl());
        $this->assertStringEndsWith('catalog/product/view/', $this->_model->getUrlInStore());
        $this->_model->setId(999);
        $url = $this->_model->getProductUrl();
        $this->assertContains('catalog/product/view', $url);
        $this->assertContains('id/999', $url);
        $storeUrl = $this->_model->getUrlInStore();
        $this->assertEquals($storeUrl, $url);
    }

    /**
     * @see Magento_Catalog_Model_Product_UrlTest
     */
    public function testFormatUrlKey()
    {
        $this->assertEquals('test', $this->_model->formatUrlKey('test'));
    }

    public function testGetUrlPath()
    {
        $this->_model->setUrlPath('test');
        $this->assertEquals('test', $this->_model->getUrlPath());

        /** @var $category Magento_Catalog_Model_Category */
        $category = Mage::getModel('Magento_Catalog_Model_Category');
        $category->setUrlPath('category');
        $this->assertEquals('category/test', $this->_model->getUrlPath($category));
    }

    /**
     * @covers Magento_Catalog_Model_Product::addOption
     * @covers Magento_Catalog_Model_Product::getOptionById
     * @covers Magento_Catalog_Model_Product::getOptions
     */
    public function testOptionApi()
    {
        $this->assertEquals(array(), $this->_model->getOptions());

        $optionId = uniqid();
        $option = Mage::getModel('Magento_Catalog_Model_Product_Option',
            array('data' => array('key' => 'value'))
        );
        $option->setId($optionId);
        $this->_model->addOption($option);

        $this->assertSame($option, $this->_model->getOptionById($optionId));
        $this->assertEquals(array($optionId => $option), $this->_model->getOptions());
    }

    /**
     * @covers Magento_Catalog_Model_Product::addCustomOption
     * @covers Magento_Catalog_Model_Product::setCustomOptions
     * @covers Magento_Catalog_Model_Product::getCustomOptions
     * @covers Magento_Catalog_Model_Product::getCustomOption
     * @covers Magento_Catalog_Model_Product::hasCustomOptions
     */
    public function testCustomOptionsApi()
    {
        $this->assertEquals(array(), $this->_model->getCustomOptions());
        $this->assertFalse($this->_model->hasCustomOptions());

        $this->_model->setId(99);
        $this->_model->addCustomOption('one', 'value1');
        $option = $this->_model->getCustomOption('one');
        $this->assertInstanceOf('\Magento\Object', $option);
        $this->assertEquals($this->_model->getId(), $option->getProductId());
        $this->assertSame($option->getProduct(), $this->_model);
        $this->assertEquals('one', $option->getCode());
        $this->assertEquals('value1', $option->getValue());

        $this->assertEquals(array('one' => $option), $this->_model->getCustomOptions());
        $this->assertTrue($this->_model->hasCustomOptions());

        $this->_model->setCustomOptions(array('test'));
        $this->assertTrue(is_array($this->_model->getCustomOptions()));
    }

    public function testCanBeShowInCategory()
    {
        $this->_model->load(1); // fixture
        $this->assertFalse((bool)$this->_model->canBeShowInCategory(6));
        $this->assertTrue((bool)$this->_model->canBeShowInCategory(3));
    }

    public function testGetAvailableInCategories()
    {
        $this->assertEquals(array(), $this->_model->getAvailableInCategories());
        $this->_model->load(1); // fixture
        $actualCategoryIds = $this->_model->getAvailableInCategories();
        sort($actualCategoryIds); // not depend on the order of items
        $this->assertEquals(array(2, 3, 4), $actualCategoryIds);
    }
}
