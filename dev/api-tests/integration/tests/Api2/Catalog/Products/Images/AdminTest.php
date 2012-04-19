<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
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
    static protected $_ioAdapter;
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
        $imageData = $imageData['full_create'];
        $pathPrefix = '/' . substr($imageData['file_name'], 0, 1) . '/' . substr($imageData['file_name'], 1, 1) . '/';
        $file = $pathPrefix . $imageData['file_name'] . '.jpg';

        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        $restResponse = $this->callPost('products/' . $product->getId() . '/images', $imageData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->load($product->getId());

        unset($imageData['file_content']);
        $createdImageData = $this->_getProductImageData($product, $file);
        $this->_checkImageData($imageData, $createdImageData);
    }

    /**
     * Test image create with empty image file
     *
     * @magentoDataFixture Api2/Catalog/Products/Images/_fixtures/product_simple.php
     */
    public function testPostEmptyFileContent()
    {
        $imageData = require dirname(__FILE__) . '/_fixtures/Backend/ImageData.php';
        $imageData = $imageData['create_with_empty_file_content'];

        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        $restResponse = $this->callPost('products/' . $product->getId() . '/images', $imageData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $errors = $body['messages']['error'];
        $this->assertNotEmpty($errors);

        $expectedErrors = array(
            'The image is not specified',
            'Resource data pre-validation error.'
        );

        $this->assertEquals(count($expectedErrors), count($errors));
        foreach ($errors as $error) {
            $this->assertContains($error['message'], $expectedErrors);
        }
    }

    /**
     * Test image create with invalide image (but valid base64 string)
     *
     * @magentoDataFixture Api2/Catalog/Products/Images/_fixtures/product_simple.php
     */
    public function testPostInvalideImage()
    {
        $imageData = require dirname(__FILE__) . '/_fixtures/Backend/ImageData.php';
        $imageData = $imageData['create_with_invalid_image'];

        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        $restResponse = $this->callPost('products/' . $product->getId() . '/images', $imageData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $errors = $body['messages']['error'];
        $this->assertNotEmpty($errors);

        $expectedErrors = array(
            'Resource unknown error.'
        );

        $this->assertEquals(count($expectedErrors), count($errors));
        foreach ($errors as $error) {
            $this->assertContains($error['message'], $expectedErrors);
        }
    }

    /**
     * Test image create with invalide base64 string as image file content
     *
     * @magentoDataFixture Api2/Catalog/Products/Images/_fixtures/product_simple.php
     */
    public function testPostInvalideBase64()
    {
        $imageData = require dirname(__FILE__) . '/_fixtures/Backend/ImageData.php';
        $imageData = $imageData['create_with_invalid_base64'];

        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        $restResponse = $this->callPost('products/' . $product->getId() . '/images', $imageData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $errors = $body['messages']['error'];
        $this->assertNotEmpty($errors);

        $expectedErrors = array(
            'The image content must be valid base64 encoded data'
        );

        $this->assertEquals(count($expectedErrors), count($errors));
        foreach ($errors as $error) {
            $this->assertContains($error['message'], $expectedErrors);
        }
    }

    /**
     * Test image create with empty image file
     *
     * @magentoDataFixture Api2/Catalog/Products/Images/_fixtures/product_simple.php
     */
    public function testPostInvalideMime()
    {
        $imageData = require dirname(__FILE__) . '/_fixtures/Backend/ImageData.php';
        $imageData = $imageData['create_with_invalid_mime'];

        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        $restResponse = $this->callPost('products/' . $product->getId() . '/images', $imageData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $errors = $body['messages']['error'];
        $this->assertNotEmpty($errors);

        $expectedErrors = array(
            'Unsuppoted image MIME type'
        );

        $this->assertEquals(count($expectedErrors), count($errors));
        foreach ($errors as $error) {
            $this->assertContains($error['message'], $expectedErrors);
        }
    }

    /**
     * Test image update
     *
     * @magentoDataFixture Api2/Catalog/Products/Images/_fixtures/product_simple.php
     */
    public function testPut()
    {
        $imageData = require dirname(__FILE__) . '/_fixtures/Backend/ImageData.php';
        $updateImageData = $imageData['data_set_2'];
        $imageData = $imageData['data_set_1'];

        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        list($file, $fileFixture) = $this->_getImageFixture();
        $imageId = $this->_addImage($product, $fileFixture, $imageData);
        $restResponse = $this->callPut('products/' . $product->getId() . '/images/' . $imageId, $updateImageData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->setStoreId(0)->load($product->getId());

        $updatedImageData = $this->_getProductImageData($product, $file);
        $this->_checkImageData($updateImageData, $updatedImageData);
    }

    /**
     * Test list images
     *
     * @magentoDataFixture Api2/Catalog/Products/Images/_fixtures/product_simple.php
     */
    public function testList()
    {
        $imageData = require dirname(__FILE__) . '/_fixtures/Backend/ImageData.php';

        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        $fileNames = array();
        $fileFixtures = array();
        for ($i=1; $i<=3; $i++) {
            /* @var $product Mage_Catalog_Model_Product */
            $product = Mage::getModel('catalog/product')->setStoreId(0)->load($product->getId());
            list($fileNames[$i], $fileFixtures[$i]) = $this->_getImageFixture();
            $this->_addImage($product, $fileFixtures[$i], $imageData['data_set_' . $i]);
        }

        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->setStoreId(0)->load($product->getId());
        $restResponse = $this->callGet('products/' . $product->getId() . '/images');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        $body = $restResponse->getBody();
        foreach ($fileNames as $key => $fileName) {
            $found = false;
            foreach ($body as $image) {
                if (isset($image['url']) && strstr($image['url'], $fileName)) {
                    $this->_checkImageData($imageData['data_set_' . $key], $image);
                    $found = true;
                    break;
                }
            }
            $this->assertTrue($found, 'Image ' . $fileName . ' not attached');
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
        $imageData = $imageData['data_set_1'];

        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        list($file, $fileFixture) = $this->_getImageFixture();
        $imageId = $this->_addImage($product, $fileFixture, $imageData);

        $restResponse = $this->callGet('products/' . $product->getId() . '/images/' . $imageId);
        $body = $restResponse->getBody();
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        $this->_checkImageData($imageData, $body);
    }


    /**
     * Test image delete
     *
     * @magentoDataFixture Api2/Catalog/Products/Images/_fixtures/product_simple.php
     */
    public function testDelete()
    {
        $imageData = require dirname(__FILE__) . '/_fixtures/Backend/ImageData.php';
        $imageData = $imageData['data_set_1'];

        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        list($file, $fileFixture) = $this->_getImageFixture();
        $imageId = $this->_addImage($product, $fileFixture, $imageData);

        $restResponse = $this->callDelete('products/' . $product->getId() . '/images/' . $imageId);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->setStoreId(0)->load($product->getId());
        self::setFixture('product_simple', $product);

        $gallery = $product->getData('media_gallery');
        $this->assertEmpty($gallery['images']);
    }

    /**
     * Test image create with store id with no image added for admin store.
     * Image should be created but its data (label, types...) should be set
     * only for passed store id. For admin store image data should have default values:
     * value_id => 120
     * label    => empty
     * position => empty
     * disabled => empty (exclude => empty)
     * types    => array()
     *
     * @magentoDataFixture Api2/Catalog/Products/Images/_fixtures/product_simple_on_new_store.php
     */
    public function testPostWithStore()
    {
        $imageData = require dirname(__FILE__) . '/_fixtures/Backend/ImageData.php';
        $imageData = $imageData['full_create'];
        $pathPrefix = '/' . substr($imageData['file_name'], 0, 1) . '/' . substr($imageData['file_name'], 1, 1) . '/';
        $file = $pathPrefix . $imageData['file_name'] . '.jpg';

        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        /* @var $product Mage_Core_Model_Store */
        $store = self::getFixture('store');
        $resourceUri = 'products/' . $product->getId() . '/images/store/' . $store->getId();
        $restResponse = $this->callPost($resourceUri, $imageData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        // check image data on defined store
        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->setStoreId($store->getId())->load($product->getId());
        unset($imageData['file_content']);
        $createdImageData = $this->_getProductImageData($product, $file);
        $this->_checkImageData($imageData, $createdImageData);

        // check image data on default store, it should be empty
        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->load($product->getId());
        $createdImageData = $this->_getProductImageData($product, $file);
        $imageDefaultData = array(
            'label' => '',
            'position' => null,
            'exclude' => null,
            'types' => array()
        );
        $this->_checkImageData($imageDefaultData, $createdImageData);
    }

    /**
     * Test image get
     *
     * @magentoDataFixture Api2/Catalog/Products/Images/_fixtures/product_simple_on_new_store.php
     */
    public function testGetWithStore()
    {
        $imageData = require dirname(__FILE__) . '/_fixtures/Backend/ImageData.php';
        $imageDataAnotherStore = $imageData['data_set_2'];
        $imageData = $imageData['data_set_1'];

        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        /* @var $product Mage_Core_Model_Store */
        $store = self::getFixture('store');

        list($file, $fileFixture) = $this->_getImageFixture();
        // add image and set image data for admin store
        $imageId = $this->_addImage($product, $fileFixture, $imageData);

        // set image data in another store
        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->setStoreId($store->getId())->load($product->getId());
        $this->_updateImage($product, $file, $imageDataAnotherStore, $store->getId());

        // check image data in admin store
        $resourceUri = 'products/' . $product->getId() . '/images/' . $imageId . '/store/0';
        $restResponse = $this->callGet($resourceUri);
        $body = $restResponse->getBody();
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        $this->_checkImageData($imageData, $body);

        // check image data in another store
        $resourceUri = 'products/' . $product->getId() . '/images/' . $imageId . '/store/' . $store->getId();
        $restResponse = $this->callGet($resourceUri);
        $body = $restResponse->getBody();
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        // types in non admin store are equal to admin store by default
        // so if we set to non admin store some type, result will be merged with types of admin store
        $imageDataAnotherStore['types'] = array_merge($imageData['types'], $imageDataAnotherStore['types']);
        $this->_checkImageData($imageDataAnotherStore, $body);
    }

    /**
     * Test image get
     */
    public function testGetCollectionWithInvalideProduct()
    {
        $resourceUri = 'products/12099999/images/';
        $restResponse = $this->callGet($resourceUri);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }

    /**
     * Test image get
     *
     * @magentoDataFixture Api2/Catalog/Products/Images/_fixtures/product_simple.php
     */
    public function testGetWithInvalideStore()
    {
        $imageData = require dirname(__FILE__) . '/_fixtures/Backend/ImageData.php';
        $imageData = $imageData['data_set_1'];

        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        list($file, $fileFixture) = $this->_getImageFixture();
        $imageId = $this->_addImage($product, $fileFixture, $imageData);

        $resourceUri = 'products/' . $product->getId() . '/images/' . $imageId . '/store/12099999';
        $restResponse = $this->callGet($resourceUri);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
    }

    /**
     * Test image get for store which is not related to product
     *
     * @magentoDataFixture Api2/Catalog/Products/Categories/_fixtures/new_category_on_new_store.php
     * @magentoDataFixture Api2/Catalog/Products/Images/_fixtures/product_simple.php
     */
    public function testGetWithNotAssignedStore()
    {
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');
        /* @var $store Mage_Core_Model_Store */
        $store = Magento_Test_Webservice::getFixture('store');

        list($file, $fileFixture) = $this->_getImageFixture();
        $imageId = $this->_addImage($product, $fileFixture, $imageData);

        $resourceUri = 'products/' . $product->getId() . '/images/' . $imageId . '/store/' . $store->getId();
        $restResponse = $this->callGet($resourceUri);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }


    /**
     * Add image to product. Return added image id
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $filePath
     * @param array $data
     * @param int $store
     * @return int
     */
    protected function _addImage($product, $filePath, $data, $store = null)
    {
        $product->addImageToMediaGallery($filePath, null, false, false);
        // file is no longer needed
        $this->_getIoAdapter()->rm($filePath);

        // get short file for added image
        $gallery = $product->getData('media_gallery');
        $this->assertNotEmpty($gallery);
        $this->assertTrue(isset($gallery['images']));
        $added = false;
        $file = null;
        foreach ($gallery['images'] as $image) {
            if (strstr($filePath, substr($image['file'], 5))) {
                $added = true;
                $file = $image['file'];
                break;
            }
        }
        $this->assertTrue($added, 'Image not added');
        $this->_updateImage($product, $file, $data, $store);

        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->setStoreId((int) $store)->load($product->getId());

        // get added image id
        $gallery = $product->getData('media_gallery');
        $this->assertNotEmpty($gallery);
        $this->assertTrue(isset($gallery['images']));
        $imageId = null;
        foreach ($gallery['images'] as $image) {
            if ($image['file'] == $file) {
                $imageId = $image['value_id'];
                break;
            }
        }

        return $imageId;
    }

    /**
     * Update product image data by its file name
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $file
     * @param array $data
     * @param int $store
     * @return \Api2_Catalog_Products_Images_AdminTest
     */
    protected function _updateImage($product, $file, $data, $store = null)
    {
        /* @var $gallery Mage_Catalog_Model_Resource_Eav_Attribute */
        $mediaModel = $this->_getMediaModel($product);

        $mediaModel->updateImage($product, $file, $data);
        $mediaModel->setMediaAttribute($product, $data['types'], $file);
        $product->setStoreId((int) $store)->save();

        return $this;
    }

    /**
     * Get backend model for media gallery attribute
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Product_Attribute_Backend_Media
     */
    protected function _getMediaModel($product)
    {
        $attributes = $product->getTypeInstance(true)->getSetAttributes($product);
        $this->assertTrue(isset($attributes['media_gallery']));
        /* @var $gallery Mage_Catalog_Model_Resource_Eav_Attribute */
        $gallery = $attributes['media_gallery'];

        return $gallery->getBackend();
    }

    /**
     * Get tool for file system manipulation
     *
     * @return Varien_Io_File
     */
    protected function _getIoAdapter()
    {
        if (!self::$_ioAdapter) {
            self::$_ioAdapter = new Varien_Io_File();
        }
        return self::$_ioAdapter;
    }

    /**
     * Create tmp image file. Return its name (/p/r/product_image4f6868eec0ca1.jpg) and full path
     *
     * @return array
     */
    protected function _getImageFixture()
    {
        $pathPrefix = '/p/r/';

        $fileName = 'product_image' . uniqid() . mt_rand(10, 99) . '.jpg';
        $fileFixture = dirname(__FILE__) . '/_fixtures/' . $fileName;
        $this->_getIoAdapter()->cp(dirname(__FILE__) . '/_fixtures/product.jpg', $fileFixture);

        return array($pathPrefix . $fileName,  $fileFixture);
    }

    /**
     * Retrieve image data (specified by file) from product
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $file
     * @return array
     */
    protected function _getProductImageData($product, $file)
    {
        $gallery = $product->getData('media_gallery');
        $this->assertNotEmpty($gallery);

        // get image data by its file
        $data = array();
        foreach ($gallery['images'] as $image) {
            if ($image['file'] == $file) {
                $data = $image;
                break;
            }
        }
        $data['exclude'] = $data['disabled'];

        // get image types settings for product
        $types = array();
        foreach ($product->getMediaAttributes() as $attribute) {
            if ($product->getData($attribute->getAttributeCode()) == $file) {
                $types[] = $attribute->getAttributeCode();
            }
        }
        $data['types'] = $types;

        return $data;
    }

    /**
     * Compare image data with expected data
     *
     * @param array $expectedData
     * @param array $data
     */
    protected function _checkImageData($expectedData, $data)
    {
        $this->assertTrue(array_key_exists('label', $data), '"Label" attribute is not exists in image data');
        $this->assertTrue(array_key_exists('position', $data), '"Position" attribute is not exists in image data');
        $this->assertTrue(array_key_exists('exclude', $data), '"Exclude" attribute is not exists in image data');
        $this->assertTrue(array_key_exists('types', $data), '"Types" attribute is not exists in image data');
        if (array_key_exists($data['url'])) {
            $this->assertContains($expectedData['file'], $data['url'], 'Image has wrong "url"');
        }
        $this->assertEquals($expectedData['label'], $data['label'], 'Image has wrong value of "label" attribute');
        $this->assertEquals((int)$expectedData['position'], (int) $data['position'],
            'Image has wrong value of "position" attribute');
        $this->assertEquals((int)$expectedData['exclude'], (int) $data['exclude'],
            'Image has wrong value of "exclude" attribute');

        foreach ($data['types'] as $type) {
            $found = false;
            foreach ($expectedData['types'] as $realType) {
                if ($type == $realType) {
                    $found = true;
                    break;
                }
            }
            $this->assertTrue($found, 'Type "' . $type . '" not found in types attribute');
        }
    }
}
