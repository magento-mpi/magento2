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

namespace Magento\Catalog\Model;

/**
 * Tests product model:
 * - external interaction is tested
 *
 * @see \Magento\Catalog\Model\ProductTest
 * @see \Magento\Catalog\Model\ProductPriceTest
 * @magentoDataFixture Magento/Catalog/_files/categories.php
 */
class ProductExternalTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Product');
    }

    public function testGetStoreId()
    {
        $this->assertEquals(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Store\Model\StoreManagerInterface')
                ->getStore()->getId(),
            $this->_model->getStoreId()
        );
        $this->_model->setData('store_id', 999);
        $this->assertEquals(999, $this->_model->getStoreId());
    }

    public function testGetLinkInstance()
    {
        $model = $this->_model->getLinkInstance();
        $this->assertInstanceOf('Magento\Catalog\Model\Product\Link', $model);
        $this->assertSame($model, $this->_model->getLinkInstance());
    }

    public function testGetCategoryId()
    {
        $this->assertFalse($this->_model->getCategoryId());
        $category = new \Magento\Object(array('id' => 5));
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Registry')->register('current_category', $category);
        try {
            $this->assertEquals(5, $this->_model->getCategoryId());
            $objectManager->get('Magento\Registry')->unregister('current_category');
        } catch (\Exception $e) {
            $objectManager->get('Magento\Registry')->unregister('current_category');
            throw $e;
        }
    }

    public function testGetCategory()
    {
        $this->assertEmpty($this->_model->getCategory());

        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Registry')
            ->register('current_category', new \Magento\Object(array('id' => 3))); // fixture
        try {
            $category = $this->_model->getCategory();
            $this->assertInstanceOf('Magento\Catalog\Model\Category', $category);
            $this->assertEquals(3, $category->getId());
            $objectManager->get('Magento\Registry')->unregister('current_category');
        } catch (\Exception $e) {
            $objectManager->get('Magento\Registry')->unregister('current_category');
            throw $e;
        }

        $categoryTwo = new \StdClass;
        $this->_model->setCategory($categoryTwo);
        $this->assertSame($categoryTwo, $this->_model->getCategory());
    }

    public function testGetCategoryIds()
    {
        // none
        /** @var $model \Magento\Catalog\Model\Product */
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Product');
        $this->assertEquals(array(), $model->getCategoryIds());

        // fixture
        $this->_model->setId(1);
        $this->assertEquals(array(2, 3, 4), $this->_model->getCategoryIds());
    }

    public function testGetCategoryCollection()
    {
        // empty
        $collection = $this->_model->getCategoryCollection();
        $this->assertInstanceOf('Magento\Catalog\Model\Resource\Category\Collection', $collection);

        // fixture
        $this->_model->setId(1);
        $fixtureCollection = $this->_model->getCategoryCollection();
        $this->assertInstanceOf('Magento\Catalog\Model\Resource\Category\Collection', $fixtureCollection);
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
        /** @var $model \Magento\Catalog\Model\Product */
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Product',
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
        /** @var $model \Magento\Catalog\Model\Product */
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Product',
            array('data' => array('store_ids' => array(1, 2)))
        );
        $this->assertEquals(array(1, 2), $model->getStoreIds());

        // fixture
        $this->_model->setId(1);
        $this->assertEquals(array(1), $this->_model->getStoreIds());
    }

    /**
     * @covers \Magento\Catalog\Model\Product::getRelatedProducts
     * @covers \Magento\Catalog\Model\Product::getRelatedProductIds
     * @covers \Magento\Catalog\Model\Product::getRelatedProductCollection
     * @covers \Magento\Catalog\Model\Product::getRelatedLinkCollection
     */
    public function testRelatedProductsApi()
    {
        $this->assertEquals(array(), $this->_model->getRelatedProducts());
        $this->assertEquals(array(), $this->_model->getRelatedProductIds());

        $collection = $this->_model->getRelatedProductCollection();
        $this->assertInstanceOf('Magento\Catalog\Model\Resource\Product\Collection', $collection);
        $this->assertSame($this->_model, $collection->getProduct());

        $linkCollection = $this->_model->getRelatedLinkCollection();
        $this->assertInstanceOf('Magento\Catalog\Model\Resource\Product\Link\Collection', $linkCollection);
        $this->assertSame($this->_model, $linkCollection->getProduct());
    }

    /**
     * @covers \Magento\Catalog\Model\Product::getUpSellProducts
     * @covers \Magento\Catalog\Model\Product::getUpSellProductIds
     * @covers \Magento\Catalog\Model\Product::getUpSellProductCollection
     * @covers \Magento\Catalog\Model\Product::getUpSellLinkCollection
     */
    public function testUpSellProductsApi()
    {
        $this->assertEquals(array(), $this->_model->getUpSellProducts());
        $this->assertEquals(array(), $this->_model->getUpSellProductIds());

        $collection = $this->_model->getUpSellProductCollection();
        $this->assertInstanceOf('Magento\Catalog\Model\Resource\Product\Collection', $collection);
        $this->assertSame($this->_model, $collection->getProduct());

        $linkCollection = $this->_model->getUpSellLinkCollection();
        $this->assertInstanceOf('Magento\Catalog\Model\Resource\Product\Link\Collection', $linkCollection);
        $this->assertSame($this->_model, $linkCollection->getProduct());
    }

    /**
     * @covers \Magento\Catalog\Model\Product::getCrossSellProducts
     * @covers \Magento\Catalog\Model\Product::getCrossSellProductIds
     * @covers \Magento\Catalog\Model\Product::getCrossSellProductCollection
     * @covers \Magento\Catalog\Model\Product::getCrossSellLinkCollection
     */
    public function testCrossSellProductsApi()
    {
        $this->assertEquals(array(), $this->_model->getCrossSellProducts());
        $this->assertEquals(array(), $this->_model->getCrossSellProductIds());

        $collection = $this->_model->getCrossSellProductCollection();
        $this->assertInstanceOf('Magento\Catalog\Model\Resource\Product\Collection', $collection);
        $this->assertSame($this->_model, $collection->getProduct());

        $linkCollection = $this->_model->getCrossSellLinkCollection();
        $this->assertInstanceOf('Magento\Catalog\Model\Resource\Product\Link\Collection', $linkCollection);
        $this->assertSame($this->_model, $linkCollection->getProduct());
    }

    /**
     * @covers \Magento\Catalog\Model\Product::getProductUrl
     * @covers \Magento\Catalog\Model\Product::getUrlInStore
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
     * @see \Magento\Catalog\Model\Product\UrlTest
     */
    public function testFormatUrlKey()
    {
        $this->assertEquals('test', $this->_model->formatUrlKey('test'));
    }

    public function testGetUrlPath()
    {
        $this->_model->setUrlPath('test');
        $this->assertEquals('test', $this->_model->getUrlPath());

        /** @var $category \Magento\Catalog\Model\Category */
        $category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Category');
        $category->setUrlPath('category');
        $this->assertEquals('category/test', $this->_model->getUrlPath($category));
    }

    /**
     * @covers \Magento\Catalog\Model\Product::addOption
     * @covers \Magento\Catalog\Model\Product::getOptionById
     * @covers \Magento\Catalog\Model\Product::getOptions
     */
    public function testOptionApi()
    {
        $this->assertEquals(array(), $this->_model->getOptions());

        $optionId = uniqid();
        $option = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Product\Option',
            array('data' => array('key' => 'value'))
        );
        $option->setId($optionId);
        $this->_model->addOption($option);

        $this->assertSame($option, $this->_model->getOptionById($optionId));
        $this->assertEquals(array($optionId => $option), $this->_model->getOptions());
    }

    /**
     * @covers \Magento\Catalog\Model\Product::addCustomOption
     * @covers \Magento\Catalog\Model\Product::setCustomOptions
     * @covers \Magento\Catalog\Model\Product::getCustomOptions
     * @covers \Magento\Catalog\Model\Product::getCustomOption
     * @covers \Magento\Catalog\Model\Product::hasCustomOptions
     */
    public function testCustomOptionsApi()
    {
        $this->assertEquals(array(), $this->_model->getCustomOptions());
        $this->assertFalse($this->_model->hasCustomOptions());

        $this->_model->setId(99);
        $this->_model->addCustomOption('one', 'value1');
        $option = $this->_model->getCustomOption('one');
        $this->assertInstanceOf('Magento\Object', $option);
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
        $this->_model->load(4); // fixture
        $actualCategoryIds = $this->_model->getAvailableInCategories();
        sort($actualCategoryIds); // not depend on the order of items
        $this->assertEquals(array(2, 10, 11, 12), $actualCategoryIds);
        //Check not visible product
        $this->_model->load(3);
        $this->assertEmpty($this->_model->getAvailableInCategories());
    }
}
