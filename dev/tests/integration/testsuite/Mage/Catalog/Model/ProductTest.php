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
 *
 * @group module:Mage_Catalog
 * @magentoDataFixture Mage/Catalog/_files/categories.php
 */
class Mage_Catalog_Model_ProductTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Catalog_Model_Product
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Mage_Catalog_Model_Product;
    }

    public function testGetStoreId()
    {
        $this->assertEquals(Mage::app()->getStore()->getId(), $this->_model->getStoreId());
        $this->_model->setData('store_id', 999);
        $this->assertEquals(999, $this->_model->getStoreId());
    }

    public function testGetResourceCollection()
    {
        $collection = $this->_model->getResourceCollection();
        $this->assertInstanceOf('Mage_Catalog_Model_Resource_Product_Collection', $collection);
        $this->assertEquals($this->_model->getStoreId(), $collection->getStoreId());
    }

    public function testGetUrlModel()
    {
        $model = $this->_model->getUrlModel();
        $this->assertInstanceOf('Mage_Catalog_Model_Product_Url', $model);
        $this->assertSame($model, $this->_model->getUrlModel());
    }

    public function testGetName()
    {
        $this->assertEmpty($this->_model->getName());
        $this->_model->setName('test');
        $this->assertEquals('test', $this->_model->getName());
    }

    public function testGetPrice()
    {
        $this->assertEmpty($this->_model->getPrice());
        $this->_model->setPrice(10.0);
        $this->assertEquals(10.0, $this->_model->getPrice());
    }

    public function testGetTypeId()
    {
        $this->assertEmpty($this->_model->getTypeId());
        $this->_model->setTypeId('simple');
        $this->assertEquals('simple', $this->_model->getTypeId());
    }

    public function testGetStatus()
    {
        $this->assertEquals(Mage_Catalog_Model_Product_Status::STATUS_ENABLED, $this->_model->getStatus());
        $this->_model->setStatus(Mage_Catalog_Model_Product_Status::STATUS_DISABLED);
        $this->assertEquals(Mage_Catalog_Model_Product_Status::STATUS_DISABLED, $this->_model->getStatus());
    }

    public function testGetSetTypeInstance()
    {
        // model getter
        $model = $this->_model->getTypeInstance();
        $this->assertInstanceOf('Mage_Catalog_Model_Product_Type_Abstract', $model);
        $this->assertSame($model, $this->_model->getTypeInstance());

        // singleton getter
        $singleton = $this->_model->getTypeInstance(true);
        $this->assertInstanceOf('Mage_Catalog_Model_Product_Type_Abstract', $singleton);
        $this->assertNotSame($model, $this->_model->getTypeInstance(true));
        $this->assertSame($singleton, $this->_model->getTypeInstance(true));

        // model setter
        $simpleModel = new Mage_Catalog_Model_Product_Type_Simple;
        $this->_model->setTypeInstance($simpleModel);
        $this->assertSame($simpleModel, $this->_model->getTypeInstance());
        $this->assertNotSame($model, $this->_model->getTypeInstance());

        // singleton setter
        $simpleSingleton = new Mage_Catalog_Model_Product_Type_Simple;
        $this->_model->setTypeInstance($simpleSingleton, true);
        $this->assertNotSame($model, $this->_model->getTypeInstance(true));
        $this->assertNotSame($simpleModel, $this->_model->getTypeInstance(true));
        $this->assertSame($simpleSingleton, $this->_model->getTypeInstance(true));
        $this->assertSame($simpleSingleton, $this->_model->getTypeInstance(true));
    }

    public function testGetLinkInstance()
    {
        $model = $this->_model->getLinkInstance();
        $this->assertInstanceOf('Mage_Catalog_Model_Product_Link', $model);
        $this->assertSame($model, $this->_model->getLinkInstance());
    }

    public function testGetIdBySku()
    {
        $this->assertEquals(1, $this->_model->getIdBySku('simple')); // fixture
    }

    public function testGetCategoryId()
    {
        $this->assertFalse($this->_model->getCategoryId());
        $category = new Varien_Object(array('id' => 5));
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

        Mage::register('current_category', new Varien_Object(array('id' => 3))); // fixture
        try {
            $category = $this->_model->getCategory();
            $this->assertInstanceOf('Mage_Catalog_Model_Category', $category);
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

    public function testSetCategoryIds()
    {
        $this->_model->setCategoryIds('1,2,,3');
        $this->assertEquals(array(0 => 1, 1 => 2, 3 => 3), $this->_model->getData('category_ids'));
    }

    /**
     * @expectedException Mage_Core_Exception
     */
    public function testSetCategoryIdsException()
    {
        $this->_model->setCategoryIds(1);
    }

    public function testGetCategoryIds()
    {
        // none
        $model = new Mage_Catalog_Model_Product;
        $this->assertEquals(array(), $model->getCategoryIds());

        // fixture
        $this->_model->setId(1);
        $this->assertEquals(array(2, 3, 4), $this->_model->getCategoryIds());
    }

    public function testGetCategoryCollection()
    {
        // empty
        $collection = $this->_model->getCategoryCollection();
        $this->assertInstanceOf('Mage_Catalog_Model_Resource_Category_Collection', $collection);

        // fixture
        $this->_model->setId(1);
        $fixtureCollection = $this->_model->getCategoryCollection();
        $this->assertInstanceOf('Mage_Catalog_Model_Resource_Category_Collection', $fixtureCollection);
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
        $model = new Mage_Catalog_Model_Product(array('website_ids' => array(1, 2)));
        $this->assertEquals(array(1, 2), $model->getWebsiteIds());

        // fixture
        $this->_model->setId(1);
        $this->assertEquals(array(1), $this->_model->getWebsiteIds());
    }

    public function testGetStoreIds()
    {
        // set
        $model = new Mage_Catalog_Model_Product(array('store_ids' => array(1, 2)));
        $this->assertEquals(array(1, 2), $model->getStoreIds());

        // fixture
        $this->_model->setId(1);
        $this->assertEquals(array(1), $this->_model->getStoreIds());
    }

    public function testGetAttributes()
    {
        // fixture required
        $this->_model->load(1);
        $attributes = $this->_model->getAttributes();
        $this->assertArrayHasKey('name', $attributes);
        $this->assertArrayHasKey('sku', $attributes);
        $this->assertInstanceOf('Mage_Catalog_Model_Resource_Eav_Attribute', $attributes['sku']);
    }

    public function testCanAffectOptions()
    {
        $this->assertFalse($this->_model->canAffectOptions());
        $this->_model->canAffectOptions(true);
        $this->assertTrue($this->_model->canAffectOptions());
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testCRUD()
    {
        Mage::app()->setCurrentStore(Mage::app()->getStore(Mage_Core_Model_App::ADMIN_STORE_ID));
        $this->_model->setTypeId('simple')->setAttributeSetId(4)
            ->setName('Simple Product')->setSku(uniqid())->setPrice(10)
            ->setMetaTitle('meta title')->setMetaKeyword('meta keyword')->setMetaDescription('meta description')
            ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
            ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
        ;
        $crud = new Magento_Test_Entity($this->_model, array('sku' => uniqid()));
        $crud->testCrud();
    }

    public function testCleanCache()
    {
        Mage::app()->saveCache('test', 'catalog_product_999', array('catalog_product_999'));
        // potential bug: it cleans by cache tags, generated from its ID, which doesn't make much sense
        $this->_model->setId(999)->cleanCache();
        $this->assertEmpty(Mage::app()->loadCache('catalog_product_999'));
    }

    public function testGetPriceModel()
    {
        $default = $this->_model->getPriceModel();
        $this->assertInstanceOf('Mage_Catalog_Model_Product_Type_Price', $default);
        $this->assertSame($default, $this->_model->getPriceModel());

        $this->_model->setTypeId('configurable');
        $type = $this->_model->getPriceModel();
        $this->assertInstanceOf('Mage_Catalog_Model_Product_Type_Configurable_Price', $type);
        $this->assertSame($type, $this->_model->getPriceModel());
    }

    /**
     * See detailed tests at Mage_Catalog_Model_Product_Type*_PriceTest
     */
    public function testGetTierPrice()
    {
        $this->assertEquals(array(), $this->_model->getTierPrice());
    }

    /**
     * See detailed tests at Mage_Catalog_Model_Product_Type*_PriceTest
     */
    public function testGetTierPriceCount()
    {
        $this->assertEquals(0, $this->_model->getTierPriceCount());
    }

    /**
     * See detailed tests at Mage_Catalog_Model_Product_Type*_PriceTest
     */
    public function testGetFormatedTierPrice()
    {
        $this->assertEquals(array(), $this->_model->getFormatedTierPrice());
    }

    /**
     * See detailed tests at Mage_Catalog_Model_Product_Type*_PriceTest
     */
    public function testGetFormatedPrice()
    {
        $this->assertEquals('<span class="price">$0.00</span>', $this->_model->getFormatedPrice());
    }

    public function testSetGetFinalPrice()
    {
        $this->assertEquals(0, $this->_model->getFinalPrice());
        $this->_model->setFinalPrice(10);
        $this->assertEquals(10, $this->_model->getFinalPrice());
    }

    /**
     * @covers Mage_Catalog_Model_Product::getCalculatedFinalPrice
     * @covers Mage_Catalog_Model_Product::getMinimalPrice
     * @covers Mage_Catalog_Model_Product::getSpecialPrice
     * @covers Mage_Catalog_Model_Product::getSpecialFromDate
     * @covers Mage_Catalog_Model_Product::getSpecialToDate
     * @covers Mage_Catalog_Model_Product::getRequestPath
     * @covers Mage_Catalog_Model_Product::getGiftMessageAvailable
     * @covers Mage_Catalog_Model_Product::getRatingSummary
     * @dataProvider getObsoleteGettersDataProvider
     * @param string $key
     * @param string $method
     */
    public function testGetObsoleteGetters($key, $method)
    {
        $value = uniqid();
        $this->assertEmpty($this->_model->$method());
        $this->_model->setData($key, $value);
        $this->assertEquals($value, $this->_model->$method());
    }

    public function getObsoleteGettersDataProvider()
    {
        return array(
            array('calculated_final_price', 'getCalculatedFinalPrice'),
            array('minimal_price', 'getMinimalPrice'),
            array('special_price', 'getSpecialPrice'),
            array('special_from_date', 'getSpecialFromDate'),
            array('special_to_date', 'getSpecialToDate'),
            array('request_path', 'getRequestPath'),
            array('gift_message_available', 'getGiftMessageAvailable'),
            array('rating_summary', 'getRatingSummary'),
        );
    }

    /**
     * @covers Mage_Catalog_Model_Product::getRelatedProducts
     * @covers Mage_Catalog_Model_Product::getRelatedProductIds
     * @covers Mage_Catalog_Model_Product::getRelatedProductCollection
     * @covers Mage_Catalog_Model_Product::getRelatedLinkCollection
     */
    public function testRelatedProductsApi()
    {
        $this->assertEquals(array(), $this->_model->getRelatedProducts());
        $this->assertEquals(array(), $this->_model->getRelatedProductIds());

        $collection = $this->_model->getRelatedProductCollection();
        $this->assertInstanceOf('Mage_Catalog_Model_Resource_Product_Collection', $collection);
        $this->assertSame($this->_model, $collection->getProduct());

        $linkCollection = $this->_model->getRelatedLinkCollection();
        $this->assertInstanceOf('Mage_Catalog_Model_Resource_Product_Link_Collection', $linkCollection);
        $this->assertSame($this->_model, $linkCollection->getProduct());
    }

    /**
     * @covers Mage_Catalog_Model_Product::getUpSellProducts
     * @covers Mage_Catalog_Model_Product::getUpSellProductIds
     * @covers Mage_Catalog_Model_Product::getUpSellProductCollection
     * @covers Mage_Catalog_Model_Product::getUpSellLinkCollection
     */
    public function testUpSellProductsApi()
    {
        $this->assertEquals(array(), $this->_model->getUpSellProducts());
        $this->assertEquals(array(), $this->_model->getUpSellProductIds());

        $collection = $this->_model->getUpSellProductCollection();
        $this->assertInstanceOf('Mage_Catalog_Model_Resource_Product_Collection', $collection);
        $this->assertSame($this->_model, $collection->getProduct());

        $linkCollection = $this->_model->getUpSellLinkCollection();
        $this->assertInstanceOf('Mage_Catalog_Model_Resource_Product_Link_Collection', $linkCollection);
        $this->assertSame($this->_model, $linkCollection->getProduct());
    }

    /**
     * @covers Mage_Catalog_Model_Product::getCrossSellProducts
     * @covers Mage_Catalog_Model_Product::getCrossSellProductIds
     * @covers Mage_Catalog_Model_Product::getCrossSellProductCollection
     * @covers Mage_Catalog_Model_Product::getCrossSellLinkCollection
     */
    public function testCrossSellProductsApi()
    {
        $this->assertEquals(array(), $this->_model->getCrossSellProducts());
        $this->assertEquals(array(), $this->_model->getCrossSellProductIds());

        $collection = $this->_model->getCrossSellProductCollection();
        $this->assertInstanceOf('Mage_Catalog_Model_Resource_Product_Collection', $collection);
        $this->assertSame($this->_model, $collection->getProduct());

        $linkCollection = $this->_model->getCrossSellLinkCollection();
        $this->assertInstanceOf('Mage_Catalog_Model_Resource_Product_Link_Collection', $linkCollection);
        $this->assertSame($this->_model, $linkCollection->getProduct());
    }

    public function testGetGroupedLinkCollection()
    {
        $linkCollection = $this->_model->getGroupedLinkCollection();
        $this->assertInstanceOf('Mage_Catalog_Model_Resource_Product_Link_Collection', $linkCollection);
        $this->assertSame($this->_model, $linkCollection->getProduct());
    }

    public function testGetMediaAttributes()
    {
        $model = new Mage_Catalog_Model_Product(array('media_attributes' => 'test'));
        $this->assertEquals('test', $model->getMediaAttributes());

        $attributes = $this->_model->getMediaAttributes();
        $this->assertArrayHasKey('image', $attributes);
        $this->assertArrayHasKey('small_image', $attributes);
        $this->assertArrayHasKey('thumbnail', $attributes);
        $this->assertInstanceOf('Mage_Catalog_Model_Resource_Eav_Attribute', $attributes['image']);
    }

    public function testGetMediaGalleryImages()
    {
        $model = new Mage_Catalog_Model_Product;
        $this->assertEmpty($model->getMediaGalleryImages());

        $this->_model->setMediaGallery(array('images' => array(array('file' => 'magento_image.jpg'))));
        $images = $this->_model->getMediaGalleryImages();
        $this->assertInstanceOf('Varien_Data_Collection', $images);
        foreach ($images as $image) {
            $this->assertInstanceOf('Varien_Object', $image);
            $image = $image->getData();
            $this->assertArrayHasKey('file', $image);
            $this->assertArrayHasKey('url', $image);
            $this->assertArrayHasKey('id', $image);
            $this->assertArrayHasKey('path', $image);
            $this->assertStringEndsWith('magento_image.jpg', $image['file']);
            $this->assertStringEndsWith('magento_image.jpg', $image['url']);
            $this->assertStringEndsWith('magento_image.jpg', $image['path']);
        }
    }

    public function testAddImageToMediaGallery()
    {
            $this->_model->addImageToMediaGallery(dirname(__DIR__) . '/_files/magento_image.jpg');
            $gallery = $this->_model->getData('media_gallery');
            $this->assertNotEmpty($gallery);
            $this->assertTrue(isset($gallery['images'][0]['file']));
            $this->assertStringStartsWith('/m/a/magento_image', $gallery['images'][0]['file']);
            $this->assertTrue(isset($gallery['images'][0]['position']));
            $this->assertTrue(isset($gallery['images'][0]['disabled']));
            $this->assertArrayHasKey('label', $gallery['images'][0]);
    }

    public function testGetMediaConfig()
    {
        $model = $this->_model->getMediaConfig();
        $this->assertInstanceOf('Mage_Catalog_Model_Product_Media_Config', $model);
        $this->assertSame($model, $this->_model->getMediaConfig());
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testDuplicate()
    {
        $undo = function ($duplicate) {
            Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);
            $duplicate->delete();
        };

        $this->_model->load(1); // fixture
        $duplicate = $this->_model->duplicate();
        try {
            $this->assertNotEmpty($duplicate->getId());
            $this->assertNotEquals($duplicate->getId(), $this->_model->getId());
            $this->assertNotEquals($duplicate->getSku(), $this->_model->getSku());
            $this->assertEquals(Mage_Catalog_Model_Product_Status::STATUS_DISABLED, $duplicate->getStatus());
            $undo($duplicate);
        } catch (Exception $e) {
            $undo($duplicate);
            throw $e;
        }
    }

    /**
     * @covers Mage_Catalog_Model_Product::isGrouped
     * @covers Mage_Catalog_Model_Product::isSuperGroup
     * @covers Mage_Catalog_Model_Product::isSuper
     */
    public function testIsGrouped()
    {
        $this->assertFalse($this->_model->isGrouped());
        $this->assertFalse($this->_model->isSuperGroup());
        $this->assertFalse($this->_model->isSuper());
        $this->_model->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_GROUPED);
        $this->assertTrue($this->_model->isGrouped());
        $this->assertTrue($this->_model->isSuperGroup());
        $this->assertTrue($this->_model->isSuper());
    }

    /**
     * @covers Mage_Catalog_Model_Product::isConfigurable
     * @covers Mage_Catalog_Model_Product::isSuperConfig
     * @covers Mage_Catalog_Model_Product::isSuper
     */
    public function testIsConfigurable()
    {
        $this->assertFalse($this->_model->isConfigurable());
        $this->assertFalse($this->_model->isSuperConfig());
        $this->assertFalse($this->_model->isSuper());
        $this->_model->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE);
        $this->assertTrue($this->_model->isConfigurable());
        $this->assertTrue($this->_model->isSuperConfig());
        $this->assertTrue($this->_model->isSuper());
    }

    /**
     * @covers Mage_Catalog_Model_Product::getVisibleInCatalogStatuses
     * @covers Mage_Catalog_Model_Product::getVisibleStatuses
     * @covers Mage_Catalog_Model_Product::isVisibleInCatalog
     * @covers Mage_Catalog_Model_Product::getVisibleInSiteVisibilities
     * @covers Mage_Catalog_Model_Product::isVisibleInSiteVisibility
     */
    public function testVisibilityApi()
    {
        $this->assertEquals(
            array(Mage_Catalog_Model_Product_Status::STATUS_ENABLED), $this->_model->getVisibleInCatalogStatuses()
        );
        $this->assertEquals(
            array(Mage_Catalog_Model_Product_Status::STATUS_ENABLED), $this->_model->getVisibleStatuses()
        );

        $this->_model->setStatus(Mage_Catalog_Model_Product_Status::STATUS_DISABLED);
        $this->assertFalse($this->_model->isVisibleInCatalog());

        $this->_model->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        $this->assertTrue($this->_model->isVisibleInCatalog());

        $this->assertEquals(array(
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_SEARCH,
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG,
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH
            ), $this->_model->getVisibleInSiteVisibilities()
        );

        $this->assertFalse($this->_model->isVisibleInSiteVisibility());
        $this->_model->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_SEARCH);
        $this->assertTrue($this->_model->isVisibleInSiteVisibility());
        $this->_model->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG);
        $this->assertTrue($this->_model->isVisibleInSiteVisibility());
        $this->_model->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);
        $this->assertTrue($this->_model->isVisibleInSiteVisibility());
    }

    /**
     * @covers Mage_Catalog_Model_Product::isDuplicable
     * @covers Mage_Catalog_Model_Product::setIsDuplicable
     */
    public function testIsDuplicable()
    {
        $this->assertTrue($this->_model->isDuplicable());
        $this->_model->setIsDuplicable(0);
        $this->assertFalse($this->_model->isDuplicable());
    }

    /**
     * @covers Mage_Catalog_Model_Product::isSalable
     * @covers Mage_Catalog_Model_Product::isSaleable
     * @covers Mage_Catalog_Model_Product::isAvailable
     * @covers Mage_Catalog_Model_Product::isInStock
     */
    public function testIsSalable()
    {
        $this->_model->load(1); // fixture
        $this->assertTrue((bool)$this->_model->isSalable());
        $this->assertTrue((bool)$this->_model->isSaleable());
        $this->assertTrue((bool)$this->_model->isAvailable());
        $this->assertTrue($this->_model->isInStock());
        $this->_model->setStatus(0);
        $this->assertFalse((bool)$this->_model->isSalable());
        $this->assertFalse((bool)$this->_model->isSaleable());
        $this->assertFalse((bool)$this->_model->isAvailable());
        $this->assertFalse($this->_model->isInStock());
    }

    /**
     * @covers Mage_Catalog_Model_Product::isVirtual
     * @covers Mage_Catalog_Model_Product::getIsVirtual
     */
    public function testIsVirtual()
    {
        $this->assertFalse($this->_model->isVirtual());
        $this->assertFalse($this->_model->getIsVirtual());

        $model = new Mage_Catalog_Model_Product(array('type_id' => Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL));
        $this->assertTrue($model->isVirtual());
        $this->assertTrue($model->getIsVirtual());
    }

    public function testIsRecurring()
    {
        $this->assertFalse($this->_model->isRecurring());
        $this->_model->setIsRecurring(1);
        $this->assertTrue($this->_model->isRecurring());
    }

    public function testGetAttributeText()
    {
        $this->assertNull($this->_model->getAttributeText('status'));
        $this->_model->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        $this->assertEquals('Enabled', $this->_model->getAttributeText('status'));
    }

    public function testGetCustomDesignDate()
    {
        $this->assertEquals(array('from' => null, 'to' => null), $this->_model->getCustomDesignDate());
        $this->_model->setCustomDesignFrom(1)->setCustomDesignTo(2);
        $this->assertEquals(array('from' => 1, 'to' => 2), $this->_model->getCustomDesignDate());
    }

    /**
     * @covers Mage_Catalog_Model_Product::getProductUrl
     * @covers Mage_Catalog_Model_Product::getUrlInStore
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
     * @see Mage_Catalog_Model_Product_UrlTest
     */
    public function testFormatUrlKey()
    {
        $this->assertEquals('test', $this->_model->formatUrlKey('test'));
    }

    public function testGetUrlPath()
    {
        $this->_model->setUrlPath('test');
        $this->assertEquals('test', $this->_model->getUrlPath());

        $category = new Mage_Catalog_Model_Category;
        $category->setUrlPath('category');
        $this->assertEquals('category/test', $this->_model->getUrlPath($category));
    }

    public function testToArray()
    {
        $this->assertEquals(array(), $this->_model->toArray());
        $this->_model->setSku('sku')->setName('name');
        $this->assertEquals(array('sku' => 'sku', 'name' => 'name'), $this->_model->toArray());
    }

    public function testFromArray()
    {
        $this->_model->fromArray(array('sku' => 'sku', 'name' => 'name', 'stock_item' => array('key' => 'value')));
        $this->assertEquals(array('sku' => 'sku', 'name' => 'name'), $this->_model->getData());
    }

    public function testIsComposite()
    {
        $this->assertFalse($this->_model->isComposite());

        $model = new Mage_Catalog_Model_Product(array('type_id' => Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE));
        $this->assertTrue($model->isComposite());
    }

    /**
     * @see Mage_Catalog_Model_Product_Type_SimpleTest
     */
    public function testGetSku()
    {
        $this->assertEmpty($this->_model->getSku());
        $this->_model->setSku('sku');
        $this->assertEquals('sku', $this->_model->getSku());
    }

    public function testGetWeight()
    {
        $this->assertEmpty($this->_model->getWeight());
        $this->_model->setWeight(10.22);
        $this->assertEquals(10.22, $this->_model->getWeight());
    }

    public function testGetOptionInstance()
    {
        $model = $this->_model->getOptionInstance();
        $this->assertInstanceOf('Mage_Catalog_Model_Product_Option', $model);
        $this->assertSame($model, $this->_model->getOptionInstance());
    }

    public function testGetProductOptionsCollection()
    {
        $this->assertInstanceOf(
            'Mage_Catalog_Model_Resource_Product_Option_Collection', $this->_model->getProductOptionsCollection()
        );
    }

    /**
     * @covers Mage_Catalog_Model_Product::addOption
     * @covers Mage_Catalog_Model_Product::getOptionById
     * @covers Mage_Catalog_Model_Product::getOptions
     */
    public function testOptionApi()
    {
        $this->assertEquals(array(), $this->_model->getOptions());

        $id = uniqid();
        $option = new Mage_Catalog_Model_Product_Option(array('key' => 'value'));
        $option->setId($id);
        $this->_model->addOption($option);

        $this->assertSame($option, $this->_model->getOptionById($id));
        $this->assertEquals(array($id => $option), $this->_model->getOptions());
    }

    /**
     * @covers Mage_Catalog_Model_Product::addCustomOption
     * @covers Mage_Catalog_Model_Product::setCustomOptions
     * @covers Mage_Catalog_Model_Product::getCustomOptions
     * @covers Mage_Catalog_Model_Product::getCustomOption
     * @covers Mage_Catalog_Model_Product::hasCustomOptions
     */
    public function testCustomOptionsApi()
    {
        $this->assertEquals(array(), $this->_model->getCustomOptions());
        $this->assertFalse($this->_model->hasCustomOptions());

        $this->_model->setId(99);
        $this->_model->addCustomOption('one', 'value1');
        $option = $this->_model->getCustomOption('one');
        $this->assertInstanceOf('Varien_Object', $option);
        $this->assertEquals($this->_model->getId(), $option->getProductId());
        $this->assertSame($option->getProduct(), $this->_model);
        $this->assertEquals('one', $option->getCode());
        $this->assertEquals('value1', $option->getValue());

        $this->assertEquals(array('one' => $option), $this->_model->getCustomOptions());
        $this->assertTrue($this->_model->hasCustomOptions());

        $this->_model->setCustomOptions('test');
        $this->assertEquals('test', $this->_model->getCustomOptions());
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
        $this->assertEquals(array(2, 3, 4), $this->_model->getAvailableInCategories());
    }

    public function testGetDefaultAttributeSetId()
    {
        $id = $this->_model->getDefaultAttributeSetId();
        $this->assertNotEmpty($id);
        $this->assertRegExp('/^[0-9]+$/', $id);
    }

    public function testGetReservedAttributes()
    {
        $result = $this->_model->getReservedAttributes();
        $this->assertInternalType('array', $result);
        $this->assertContains('position', $result);
        $this->assertContains('reserved_attributes', $result);
        $this->assertContains('is_virtual', $result);
        // and 84 more...

        $this->assertNotContains('type_id', $result);
        $this->assertNotContains('calculated_final_price', $result);
        $this->assertNotContains('request_path', $result);
        $this->assertNotContains('rating_summary', $result);
    }

    /**
     * @param bool $isUserDefined
     * @param string $code
     * @param bool $expectedResult
     * @dataProvider isReservedAttributeDataProvider
     */
    public function testIsReservedAttribute($isUserDefined, $code, $expectedResult)
    {
        $attribute = new Varien_Object(array('is_user_defined' => $isUserDefined, 'code' => $code));
        $this->assertEquals($expectedResult, $this->_model->isReservedAttribute($attribute));
    }

    public function isReservedAttributeDataProvider()
    {
        array(
            array(true, 'position', true),
            array(true, 'type_id', false),
            array(false, 'no_difference', false),
        );
    }

    public function testSetOrigData()
    {
        $this->assertEmpty($this->_model->getOrigData());
        $data = array('key' => 'value');
        $this->_model->setOrigData($data);
        $this->assertEmpty($this->_model->getOrigData());

        $storeId = Mage::app()->getStore()->getId();
        Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);
        try {
            $this->_model->setOrigData($data);
            $this->assertEquals($data, $this->_model->getOrigData());
        } catch (Exception $e) {
            Mage::app()->getStore()->setId($storeId);
        }
    }

    public function testReset()
    {
        $model = $this->_model;
        $testCase = $this;
        $assertEmpty = function() use ($model, $testCase) {
            $testCase->assertEquals(array(), $model->getData());
            $testCase->assertEquals(null, $model->getOrigData());
            $testCase->assertEquals(array(), $model->getCustomOptions());
            // impossible to test $_optionInstance
            $testCase->assertEquals(array(), $model->getOptions());
            $testCase->assertFalse($model->canAffectOptions());
            // impossible to test $_errors
        };
        $assertEmpty();

        $this->_model->setData('key', 'value');
        $this->_model->reset();
        $assertEmpty();

        $this->_model->setOrigData('key', 'value');
        $this->_model->reset();
        $assertEmpty();

        $this->_model->addCustomOption('key', 'value');
        $this->_model->reset();
        $assertEmpty();

        $this->_model->addOption(new Mage_Catalog_Model_Product_Option);
        $this->_model->reset();
        $assertEmpty();

        $this->_model->canAffectOptions(true);
        $this->_model->reset();
        $assertEmpty();
    }

    public function testGetCacheIdTags()
    {
        $this->assertFalse($this->_model->getCacheIdTags());

        $this->_model->load(1); // fixture
        $this->assertEquals(array('catalog_product_1'), $this->_model->getCacheIdTags());
    }

    /**
     * @magentoDataFixture Mage/Catalog/_files/two_products.php
     */
    public function testIsProductsHasSku()
    {
        $this->assertTrue($this->_model->isProductsHasSku(array(10, 11)));
    }

    public function testProcessBuyRequest()
    {
        $request = new Varien_Object;
        $result = $this->_model->processBuyRequest($request);
        $this->assertInstanceOf('Varien_Object', $result);
        $this->assertArrayHasKey('errors', $result->getData());
    }

    public function testGetPreconfiguredValues()
    {
        $this->assertInstanceOf('Varien_Object', $this->_model->getPreconfiguredValues());
        $this->_model->setPreconfiguredValues('test');
        $this->assertEquals('test', $this->_model->getPreconfiguredValues());
    }
}
