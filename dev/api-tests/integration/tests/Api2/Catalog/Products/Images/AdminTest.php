<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test product images resource
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */

class Api2_Catalog_Products_Images_AdminTest extends Magento_Test_Webservice_Rest_Admin
{
    /**
     * Delete fixtures
     */
    protected function tearDown()
    {
        self::deleteFixture('product_simple', true);
        parent::tearDown();
    }

    /**
     * Delete store fixture
     */
    public static function tearDownAfterClass()
    {
        self::deleteFixture('store', true);
        self::deleteFixture('store_group', true);
        self::deleteFixture('website', true);
        self::deleteFixture('category', true);
        parent::tearDownAfterClass();
    }

    /**
     * Test image create
     *
     * @magentoDataFixture Api2/Catalog/Products/Images/_fixtures/product_simple.php
     */
    public function testPost()
    {
        $imageData = require dirname(__FILE__) . '/_fixtures/Backend/ImageData.php';
        $imageData = $imageData['create'];
        $pathPrefix = '/' . substr($imageData['file_name'], 0, 1) . '/' . substr($imageData['file_name'], 1, 1) . '/';
        $imageFileName = $pathPrefix . $imageData['file_name'];

        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        $restResponse = $this->callPost('products/' . $product->getId() . '/images', $imageData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->load($product->getId());
        $this->assertNotNull($product->getId());
        self::setFixture('product_simple', $product);

        $gallery = $product->getData('media_gallery');
        $this->assertNotEmpty($gallery);
        $this->assertTrue(isset($gallery['images'][0]['file']));
        $this->assertStringStartsWith($imageFileName, $gallery['images'][0]['file']);
    }

    /**
     * Test image update
     *
     * @magentoDataFixture Api2/Catalog/Products/Images/_fixtures/product_simple.php
     */
    public function testPut()
    {
        $imageData = require dirname(__FILE__) . '/_fixtures/Backend/ImageData.php';
        $imageData = $imageData['update'];

        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        $ioAdapter = new Varien_Io_File();
        $fileName = 'product_image' . uniqid() . '.jpg';
        $fileFixture = dirname(__FILE__) . '/_fixtures/' . $fileName;
        $ioAdapter->cp(dirname(__FILE__) . '/_fixtures/product.jpg', $fileFixture);
        $product->addImageToMediaGallery($fileFixture, null, false, false);
        $product->setStoreId(0)->save();

        $ioAdapter->rm($fileFixture);

        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->setStoreId(0)->load($product->getId());

        $gallery = $product->getData('media_gallery');
        $this->assertNotEmpty($gallery);
        $this->assertTrue(isset($gallery['images'][0]['value_id']));
        $imageId = $gallery['images'][0]['value_id'];

        $restResponse = $this->callPut('products/' . $product->getId() . '/images/' . $imageId , $imageData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->setStoreId(0)->load($product->getId());
        $this->assertNotNull($product->getId());
        self::setFixture('product_simple', $product);

        $gallery = $product->getData('media_gallery');
        $this->assertNotEmpty($gallery);
        $this->assertTrue(isset($gallery['images'][0]['label']));
        $this->assertTrue(isset($gallery['images'][0]['position']));
        $this->assertEquals($imageData['label'], $gallery['images'][0]['label']);
        $this->assertEquals($imageData['position'], $gallery['images'][0]['position']);

        $types = array();
        foreach ($product->getMediaAttributes() as $attribute) {
            if ($product->getData($attribute->getAttributeCode()) == $gallery['images'][0]['file']) {
                $types[] = $attribute->getAttributeCode();
            }
        }
        foreach ($imageData['types'] as $type) {
            $found = false;
            foreach ($types as $realType) {
                if ($type == $realType) {
                    $found = true;
                    break;
                }
            }
            $this->assertTrue($found);
        }
    }

    /**
     * Test list images
     *
     * @magentoDataFixture Api2/Catalog/Products/Images/_fixtures/product_simple.php
     */
    public function testList()
    {
        $imagesNumber = 3;
        $pathPrefix = '/p/r/';
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        $ioAdapter = new Varien_Io_File();
        $fileNames = array();
        $fileFixtures = array();
        for ($i=0; $i<$imagesNumber; $i++) {
            $fileNames[$i] = 'product_image' . uniqid() . '.jpg';
            $fileFixtures[$i] = dirname(__FILE__) . '/_fixtures/' . $fileNames[$i];
            $ioAdapter->cp(dirname(__FILE__) . '/_fixtures/product.jpg', $fileFixtures[$i]);
            $product->addImageToMediaGallery($fileFixtures[$i], null, false, false);
        }
        $product->save();
        foreach ($fileFixtures as $fileFixture) {
            $ioAdapter->rm($fileFixture);
        }

        $restResponse = $this->callGet('products/' . $product->getId() . '/images');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        $body = $restResponse->getBody();
        foreach ($fileNames as $fileName) {
            $found = false;
            foreach ($body as $imageData) {
                if (isset($imageData['url']) && strstr($imageData['url'], $pathPrefix . $fileName)) {
                    $found = true;
                    break;
                }
            }
            $this->assertTrue($found, 'Image ' . $pathPrefix . $fileName . ' not attached');
        }
    }

    /**
     * Test image get
     *
     * @magentoDataFixture Api2/Catalog/Products/Images/_fixtures/product_simple.php
     */
    public function testGet()
    {
        $imageData = require dirname(__FILE__) . '/_fixtures/Backend/ImageData.php';
        $imageData = $imageData['update'];

        $pathPrefix = '/p/r/';
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        $ioAdapter = new Varien_Io_File();
        $fileName = 'product_image' . uniqid() . '.jpg';
        $fileFixture = dirname(__FILE__) . '/_fixtures/' . $fileName;
        $ioAdapter->cp(dirname(__FILE__) . '/_fixtures/product.jpg', $fileFixture);
        $product->addImageToMediaGallery($fileFixture, null, false, false);

        $attributes = $product->getTypeInstance(true)->getSetAttributes($product);
        $this->assertTrue(isset($attributes['media_gallery']));
        $gallery = $attributes['media_gallery'];
        /* @var $gallery Mage_Catalog_Model_Resource_Eav_Attribute */
        $gallery->getBackend()->updateImage($product, $pathPrefix . $fileName, $imageData);
        $gallery->getBackend()->setMediaAttribute($product, $imageData['types'], $pathPrefix . $fileName);
        $product->setStoreId(0)->save();

        $ioAdapter->rm($fileFixture);

        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->setStoreId(0)->load($product->getId());
        self::setFixture('product_simple', $product);

        $gallery = $product->getData('media_gallery');
        $this->assertNotEmpty($gallery);
        $this->assertTrue(isset($gallery['images'][0]['value_id']));
        $imageId = $gallery['images'][0]['value_id'];

        $restResponse = $this->callGet('products/' . $product->getId() . '/images/' . $imageId);
        $body = $restResponse->getBody();
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        $this->assertTrue(isset($body['label']));
        $this->assertTrue(isset($body['position']));
        $this->assertTrue(isset($body['url']));
        $this->assertTrue(isset($body['types']));
        $this->assertContains($pathPrefix . $fileName, $body['url']);
        $this->assertEquals($imageData['label'], $body['label']);
        $this->assertEquals($imageData['position'], $body['position']);

        $types = array();
        foreach ($product->getMediaAttributes() as $attribute) {
            if ($product->getData($attribute->getAttributeCode()) == $gallery['images'][0]['file']) {
                $types[] = $attribute->getAttributeCode();
            }
        }
        foreach ($body['types'] as $type) {
            $found = false;
            foreach ($types as $realType) {
                if ($type == $realType) {
                    $found = true;
                    break;
                }
            }
            $this->assertTrue($found);
        }
    }


    /**
     * Test image delete
     *
     * @magentoDataFixture Api2/Catalog/Products/Images/_fixtures/product_simple.php
     */
    public function testDelete()
    {
        $imageData = require dirname(__FILE__) . '/_fixtures/Backend/ImageData.php';
        $imageData = $imageData['update'];

        $pathPrefix = '/p/r/';
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        $ioAdapter = new Varien_Io_File();
        $fileName = 'product_image' . uniqid() . '.jpg';
        $fileFixture = dirname(__FILE__) . '/_fixtures/' . $fileName;
        $ioAdapter->cp(dirname(__FILE__) . '/_fixtures/product.jpg', $fileFixture);
        $product->addImageToMediaGallery($fileFixture, null, false, false);

        $attributes = $product->getTypeInstance(true)->getSetAttributes($product);
        $this->assertTrue(isset($attributes['media_gallery']));
        $gallery = $attributes['media_gallery'];
        /* @var $gallery Mage_Catalog_Model_Resource_Eav_Attribute */
        $gallery->getBackend()->updateImage($product, $pathPrefix . $fileName, $imageData);
        $gallery->getBackend()->setMediaAttribute($product, $imageData['types'], $pathPrefix . $fileName);
        $product->setStoreId(0)->save();

        $ioAdapter->rm($fileFixture);

        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->setStoreId(0)->load($product->getId());
        self::setFixture('product_simple', $product);

        $gallery = $product->getData('media_gallery');
        $this->assertNotEmpty($gallery);
        $this->assertTrue(isset($gallery['images'][0]['value_id']));
        $imageId = $gallery['images'][0]['value_id'];

        $restResponse = $this->callDelete('products/' . $product->getId() . '/images/' . $imageId);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());


        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->setStoreId(0)->load($product->getId());
        self::setFixture('product_simple', $product);

        $gallery = $product->getData('media_gallery');
        $this->assertEmpty($gallery['images']);
    }

    /**
     * Test image create
     *
     * @magentoDataFixture Api2/Catalog/Products/Images/_fixtures/product_simple_on_new_store.php
     */
    public function testPostWithStore()
    {
        $imageData = require dirname(__FILE__) . '/_fixtures/Backend/ImageData.php';
        $imageData = $imageData['create'];
        $pathPrefix = '/' . substr($imageData['file_name'], 0, 1) . '/' . substr($imageData['file_name'], 1, 1) . '/';
        $imageFileName = $pathPrefix . $imageData['file_name'];

        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        /* @var $product Mage_Core_Model_Store */
        $store = self::getFixture('store');
        $resourceUri = 'products/' . $product->getId() . '/images/store/' . $store->getId();
        $restResponse = $this->callPost($resourceUri, $imageData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->setStoreId($store->getId())->load($product->getId());
        $this->assertNotNull($product->getId());
        self::setFixture('product_simple', $product);

        $gallery = $product->getData('media_gallery');
        $this->assertNotEmpty($gallery);
        $this->assertTrue(isset($gallery['images'][0]['label']));
        $this->assertTrue(isset($gallery['images'][0]['disabled']));
        $this->assertTrue(isset($gallery['images'][0]['position']));
        $this->assertStringStartsWith($imageFileName, $gallery['images'][0]['file']);
        $this->assertEquals($imageData['label'], $gallery['images'][0]['label']);
        $this->assertEquals((int)$imageData['disabled'], (int)$gallery['images'][0]['disabled']);
        $this->assertEquals((int)$imageData['position'], (int)$gallery['images'][0]['position']);

        // check image data on default store? it should be empty

        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->load($product->getId());
        $this->assertNotNull($product->getId());

        $gallery = $product->getData('media_gallery');
        $this->assertNotEmpty($gallery);
        $this->assertTrue(isset($gallery['images'][0]['label']));
        $this->assertStringStartsWith($imageFileName, $gallery['images'][0]['file']);
        $this->assertNotEquals($imageData['label'], $gallery['images'][0]['label']);
    }
}
