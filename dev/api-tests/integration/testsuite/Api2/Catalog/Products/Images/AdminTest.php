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
     * @magentoDataFixture Api2/Catalog/Products/Images/_fixture/product_simple.php
     * @resourceOperation product_image::create
     */
    public function testPost()
    {
        $imageData = require dirname(__FILE__) . '/_fixture/Backend/ImageData.php';
        $imageData = $imageData['full_create'];
        $pathPrefix = '/' . substr($imageData['file_name'], 0, 1) . '/' . substr($imageData['file_name'], 1, 1) . '/';
        $file = $pathPrefix . $imageData['file_name'] . '.jpeg';

        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        $restResponse = $this->callPost('products/' . $product->getId() . '/images', $imageData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('Mage_Catalog_Model_Product')->load($product->getId());

        unset($imageData['file_content']);
        $createdImageData = $this->_getProductImageData($product, $file);
        $this->_checkImageData($imageData, $createdImageData);
    }


    /**
     * Test image create with some invalid data which give error message
     *
     * @param array $imageData
     * @param array $expectedErrors
     * @dataProvider dataProviderTestPostInvalidDataError
     * @magentoDataFixture Api2/Catalog/Products/Images/_fixture/product_simple.php
     * @resourceOperation product_image::create
     */
    public function testPostInvalidDataError($imageData, $expectedErrors)
    {
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        $restResponse = $this->callPost('products/' . $product->getId() . '/images', $imageData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $this->_checkErrorMessagesInResponse($restResponse, $expectedErrors);
    }

    /**
     * Data provider for testPostInvalidDataError
     *
     * @dataSetNumber 7
     * @return array
     */
    public function dataProviderTestPostInvalidDataError()
    {
        $imageBase64Content = 'iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlY'
            . 'WR5ccllPAAAAWtJREFUeNpi/P//P8NgBkwMgxyMOnDUgTDAyMhIDNYF4vNA/B+IDwCxHLoakgEoFxODiQRXQUYi4e3k2gfDjMRajs'
            . 'P3zED8F8pmA+JvUDEYeArEMugOpFcanA/Ef6A0CPwC4uNoag5SnAjJjGI2tKhkg4rLAfFGIH4IxEuBWIjSKKYkDfZCHddLiwChVho'
            . 'kK8YGohwEZYy3aBmEKmDEhOCgreomo+VmZHxsMEQxIc2MAx3FO/DI3RxMmQTZkI9ALDCaSUYdOOrAIeRAPzQ+PxCHUM2FFDb5paGN'
            . 'BPRa5C20bUhxc4sSB4JaLnvxVHWHsbVu6OnACjyOg+HqgXKgGRD/JMKBoD6LDb0dyAPE94hwHAw/hGYcujlwEQmOg+EV9HJgLBmOg'
            . '+FMWjsQVKR8psCBoDSrQqoDSSmoG6Hpj1wA6ju30LI9+BBX4UsC+Ai0T4BWVd1EIL5PgeO+APECmoXgaGtm1IE0AgABBgAJAICuV8'
            . 'dAUAAAAABJRU5ErkJggg==';

        return array(
            'create_with_empty_file_content' => array(
                array(
                    'file_name' => 'product_image' . uniqid(),
                    'file_content' => '',
                    'file_mime_type' => 'image/jpeg',
                    'label'    => 'test product image ' . uniqid(),
                    'position' => 2,
                    'types'    => array('small_image'),
                    'exclude'  => 0
                ),
                array("'file_content' is not specified.")
            ),
            'create_with_invalid_image' => array(
                array(
                    'file_name' => 'product_image' . uniqid(),
                    'file_content' => 'abdbavvghjkdgvfgsydauvsdcfbgdsy321635bhdsisat67832b32y7r82vrdsw==',
                    'file_mime_type' => 'image/jpeg',
                    'label'    => 'test product image ' . uniqid(),
                    'position' => 2,
                    'types'    => array('small_image'),
                    'exclude'  => 0
                ),
                array('File content is not an image file.')
            ),
            'create_with_no_content' => array(
                array(
                    'file_name' => 'product_image' . uniqid(),
                    'file_mime_type' => 'image/jpeg',
                    'label'    => 'test product image ' . uniqid(),
                    'position' => 2,
                    'types'    => array('small_image'),
                    'exclude'  => 0
                ),
                array("'file_content' is not specified.")
            ),
            'create_with_invalid_base64' => array(
                array(
                    'file_name' => 'product_image' . uniqid(),
                    'file_content' => 'вкегмсыпв/*-+!"№;№"%;*#@$#$%^^&fghjf fghftyu vftib',
                    'file_mime_type' => 'image/jpeg',
                    'label'    => 'test product image ' . uniqid(),
                    'position' => 2,
                    'types'    => array('small_image'),
                    'exclude'  => 0
                ),
                array('File content must be base64 encoded.')
            ),
            'create_with_invalid_mime' => array(
                array(
                    'file_name' => 'product_image' . uniqid(),
                    'file_content' => $imageBase64Content,
                    'file_mime_type' => 'plain/text',
                    'label'    => 'test product image ' . uniqid(),
                    'position' => 2,
                    'types'    => array('small_image'),
                    'exclude'  => 0
                ),
                array('Unsuppoted file MIME type')
            ),
            'create_with_invalid_mime_2' => array(
                array(
                    'file_name' => 'product_image' . uniqid(),
                    'file_content' => $imageBase64Content,
                    'file_mime_type' => array('image/jpeg'),
                    'label'    => 'test product image ' . uniqid(),
                    'position' => 2,
                    'types'    => array('small_image'),
                    'exclude'  => 0
                ),
                array('Unsuppoted file MIME type')
            ),
            'create_with_empty_mime' => array(
                array(
                    'file_name' => 'product_image' . uniqid(),
                    'file_content' => $imageBase64Content,
                    'file_mime_type' => '',
                    'label'    => 'test product image ' . uniqid(),
                    'position' => 2,
                    'types'    => array('small_image'),
                    'exclude'  => 0
                ),
                array("'file_mime_type' is not specified.")
            ),
        );
    }

    /**
     * Test image create with some invalid data which converted to valid in api without error message
     *
     * @param array $imageData
     * @param array $correctedImageData
     * @dataProvider dataProviderTestPostInvalidDataConvertedWithoutError
     * @magentoDataFixture Api2/Catalog/Products/Images/_fixture/product_simple.php
     * @resourceOperation product_image::create
     */
    public function testPostInvalidDataConvertedWithoutError($imageData, $correctedImageData)
    {
        $pathPrefix = '/' . substr($imageData['file_name'], 0, 1) . '/' . substr($imageData['file_name'], 1, 1) . '/';
        $file = $pathPrefix . $imageData['file_name'] . '.jpeg';

        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        $restResponse = $this->callPost('products/' . $product->getId() . '/images', $imageData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        $product = Mage::getModel('Mage_Catalog_Model_Product')->load($product->getId());
        unset($imageData['file_content']);
        $expectedData = array_merge($imageData, $correctedImageData);
        $createdImageData = $this->_getProductImageData($product, $file);
        $this->_checkImageData($expectedData, $createdImageData);
    }

    /**
     * Data provider for testPostInvalidDataConvertedWithoutError
     *
     * @dataSetNumber 7
     * @return array
     */
    public function dataProviderTestPostInvalidDataConvertedWithoutError()
    {
        $imageBase64Content = 'iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlY'
            . 'WR5ccllPAAAAWtJREFUeNpi/P//P8NgBkwMgxyMOnDUgTDAyMhIDNYF4vNA/B+IDwCxHLoakgEoFxODiQRXQUYi4e3k2gfDjMRajs'
            . 'P3zED8F8pmA+JvUDEYeArEMugOpFcanA/Ef6A0CPwC4uNoag5SnAjJjGI2tKhkg4rLAfFGIH4IxEuBWIjSKKYkDfZCHddLiwChVho'
            . 'kK8YGohwEZYy3aBmEKmDEhOCgreomo+VmZHxsMEQxIc2MAx3FO/DI3RxMmQTZkI9ALDCaSUYdOOrAIeRAPzQ+PxCHUM2FFDb5paGN'
            . 'BPRa5C20bUhxc4sSB4JaLnvxVHWHsbVu6OnACjyOg+HqgXKgGRD/JMKBoD6LDb0dyAPE94hwHAw/hGYcujlwEQmOg+EV9HJgLBmOg'
            . '+FMWjsQVKR8psCBoDSrQqoDSSmoG6Hpj1wA6ju30LI9+BBX4UsC+Ai0T4BWVd1EIL5PgeO+APECmoXgaGtm1IE0AgABBgAJAICuV8'
            . 'dAUAAAAABJRU5ErkJggg==';

        return array(
            'create_with_invalid_types' => array(
                array(
                    'file_name' => 'product_image' . uniqid(),
                    'file_content' => $imageBase64Content,
                    'file_mime_type' => 'image/jpeg',
                    'label'    => 'test product image ' . uniqid(),
                    'position' => 2,
                    'types'    => array('invalid'),
                    'exclude'  => 0
                ),
                array(
                    'types'    => array(),
                )
            ),
            'create_with_invalid_position' => array(
                array(
                    'file_name' => 'product_image' . uniqid(),
                    'file_content' => $imageBase64Content,
                    'file_mime_type' => 'image/jpeg',
                    'label'    => 'test product image ' . uniqid(),
                    'position' => 'invalid',
                    'types'    => array('small_image'),
                    'exclude'  => 0
                ),
                array(
                    'position' => 0,
                )
            ),
            'create_with_no_position' => array(
                array(
                    'file_name' => 'product_image' . uniqid(),
                    'file_content' => $imageBase64Content,
                    'file_mime_type' => 'image/jpeg',
                    'label'    => 'test product image ' . uniqid(),
                    'types'    => array('small_image'),
                    'exclude'  => 0
                ),
                array(
                    'position' => 1,
                )
            ),
            'create_with_invalid_exclude' => array(
                array(
                    'file_name' => 'product_image' . uniqid(),
                    'file_content' => $imageBase64Content,
                    'file_mime_type' => 'image/jpeg',
                    'label'    => 'test product image ' . uniqid(),
                    'position' => 2,
                    'types'    => array('small_image'),
                    'exclude'  => 'invalid'
                ),
                array(
                    'exclude'  => 0,
                )
            ),
            'create_with_invalid_exclude_1' => array(
                array(
                    'file_name' => 'product_image' . uniqid(),
                    'file_content' => $imageBase64Content,
                    'file_mime_type' => 'image/jpeg',
                    'label'    => 'test product image ' . uniqid(),
                    'position' => 2,
                    'types'    => array('small_image'),
                    'exclude'  => array('invalid')
                ),
                array(
                    'exclude'  => 1,
                )
            ),
            'create_with_invalid_exclude_2' => array(
                array(
                    'file_name' => 'product_image' . uniqid(),
                    'file_content' => $imageBase64Content,
                    'file_mime_type' => 'image/jpeg',
                    'label'    => 'test product image ' . uniqid(),
                    'position' => 2,
                    'types'    => array('small_image'),
                    'exclude'  => array()
                ),
                array(
                    'exclude'  => 0,
                )
            ),
            'create_with_no_exclude' => array(
                array(
                    'file_name' => 'product_image' . uniqid(),
                    'file_content' => $imageBase64Content,
                    'file_mime_type' => 'image/jpeg',
                    'label'    => 'test product image ' . uniqid(),
                    'position' => 2,
                    'types'    => array('small_image')
                ),
                array(
                    'exclude'  => 0,
                )
            ),
        );
    }

    /**
     * Test image create with invalid store id
     *
     * @magentoDataFixture Api2/Catalog/Products/Images/_fixture/product_simple.php
     * @resourceOperation product_image::create
     */
    public function testPostInvalidStoreId()
    {
        $imageData = require dirname(__FILE__) . '/_fixture/Backend/ImageData.php';
        $imageData = $imageData['full_create'];

        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        $resourceUri = 'products/' . $product->getId() . '/images/store/invalidId';
        $restResponse = $this->callPost($resourceUri, $imageData);
        $this->_checkErrorMessagesInResponse($restResponse, 'Requested store is invalid');
    }

    /**
     * Test image update
     *
     * @magentoDataFixture Api2/Catalog/Products/Images/_fixture/product_simple.php
     * @resourceOperation product_image::update
     */
    public function testPut()
    {
        $imageData = require dirname(__FILE__) . '/_fixture/Backend/ImageData.php';
        $updateImageData = $imageData['data_set_2'];
        $imageData = $imageData['data_set_1'];

        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        list($file, $fileFixture) = $this->_getImageFixture();
        $imageId = $this->_addImage($product, $fileFixture, $imageData);
        $restResponse = $this->callPut('products/' . $product->getId() . '/images/' . $imageId, $updateImageData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('Mage_Catalog_Model_Product')->setStoreId(0)->load($product->getId());

        $updatedImageData = $this->_getProductImageData($product, $file);
        $this->_checkImageData($updateImageData, $updatedImageData);
    }

    /**
     * Test image update with invalid data
     *
     * @param array $createImageData
     * @param array $updateImageData
     * @param array $correctedImageData
     * @dataProvider dataProviderTestPutInvalidData
     * @magentoDataFixture Api2/Catalog/Products/Images/_fixture/product_simple.php
     * @resourceOperation product_image::update
     */
    public function testPutInvalidData($createImageData, $updateImageData, $correctedImageData)
    {
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        list($file, $fileFixture) = $this->_getImageFixture();
        $imageId = $this->_addImage($product, $fileFixture, $createImageData);
        $restResponse = $this->callPut('products/' . $product->getId() . '/images/' . $imageId, $updateImageData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('Mage_Catalog_Model_Product')->setStoreId(0)->load($product->getId());

        $expectedData = array_merge($updateImageData, $correctedImageData);
        $updatedImageData = $this->_getProductImageData($product, $file);
        $this->_checkImageData($expectedData, $updatedImageData);
    }

    /**
     * Data provider for testPutInvalidData
     *
     * @dataSetNumber 7
     * @return array
     */
    public function dataProviderTestPutInvalidData()
    {
        $createImageData = array(
            'label' => 'test product image 1 ' . uniqid(),
            'position' => 10,
            'types'    => array('image'),
            'exclude'  => 0
        );
        return array(
            'update_with_invalid_types' => array(
                $createImageData,
                array(
                    'label'    => 'test product image ' . uniqid(),
                    'position' => 2,
                    'types'    => array('invalid'),
                    'exclude'  => 0
                ),
                array(
                    'types'    => $createImageData['types'],
                )
            ),
            'update_with_invalid_position' => array(
                $createImageData,
                array(
                    'label'    => 'test product image ' . uniqid(),
                    'position' => 'invalid',
                    'types'    => array('small_image'),
                    'exclude'  => 0
                ),
                array(
                    'position' => 0,
                )
            ),
            'update_with_no_position' => array(
                $createImageData,
                array(
                    'label'    => 'test product image ' . uniqid(),
                    'types'    => array('small_image'),
                    'exclude'  => 0
                ),
                array(
                    'position' => $createImageData['position'],
                )
            ),
            'update_with_invalid_exclude' => array(
                $createImageData,
                array(
                    'label'    => 'test product image ' . uniqid(),
                    'position' => 2,
                    'types'    => array('small_image'),
                    'exclude'  => 'invalid'
                ),
                array(
                    'exclude'  => 0,
                )
            ),
            'update_with_invalid_exclude_1' => array(
                $createImageData,
                array(
                    'label'    => 'test product image ' . uniqid(),
                    'position' => 2,
                    'types'    => array('small_image'),
                    'exclude'  => array('invalid')
                ),
                array(
                    'exclude'  => 1,
                )
            ),
            'update_with_invalid_exclude_2' => array(
                $createImageData,
                array(
                    'label'    => 'test product image ' . uniqid(),
                    'position' => 2,
                    'types'    => array('small_image'),
                    'exclude'  => array()
                ),
                array(
                    'exclude'  => 0,
                )
            ),
            'update_with_no_exclude' => array(
                $createImageData,
                array(
                    'label'    => 'test product image ' . uniqid(),
                    'position' => 2,
                    'types'    => array('small_image')
                ),
                array(
                    'exclude' => $createImageData['exclude'],
                )
            ),
        );
    }

    /**
     * Test image update for non-default store
     *
     * @magentoDataFixture Api2/Catalog/Products/Images/_fixture/product_simple_on_new_store.php
     * @resourceOperation product_image::update
     */
    public function testPutWithStore()
    {
        $imageData = require dirname(__FILE__) . '/_fixture/Backend/ImageData.php';
        $updateImageData = $imageData['data_set_2'];
        $imageData = $imageData['data_set_1'];

        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        /* @var $product Mage_Core_Model_Store */
        $store = self::getFixture('store');

        list($file, $fileFixture) = $this->_getImageFixture();
        $imageId = $this->_addImage($product, $fileFixture, $imageData);
        $resourceUri = 'products/' . $product->getId() . '/images/' . $imageId . '/store/' . $store->getId();
        $restResponse = $this->callPut($resourceUri, $updateImageData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('Mage_Catalog_Model_Product')->setStoreId(0)->load($product->getId());

        $createdImageData = $this->_getProductImageData($product, $file);
        $this->_checkImageData($imageData, $createdImageData);

        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('Mage_Catalog_Model_Product')->setStoreId($store->getId())->load($product->getId());

        $updatedImageData = $this->_getProductImageData($product, $file);
        $this->_checkImageData($updateImageData, $updatedImageData);
    }

    /**
     * Test image update with invalid store id
     *
     * @magentoDataFixture Api2/Catalog/Products/Images/_fixture/product_simple.php
     * @resourceOperation product_image::create
     */
    public function testPutInvalidStoteId()
    {
        $imageData = require dirname(__FILE__) . '/_fixture/Backend/ImageData.php';
        $updateImageData = $imageData['data_set_2'];
        $imageData = $imageData['data_set_1'];

        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        list($file, $fileFixture) = $this->_getImageFixture();
        $imageId = $this->_addImage($product, $fileFixture, $imageData);
        $resourceUri = 'products/' . $product->getId() . '/images/' . $imageId . '/store/invalidId';
        $restResponse = $this->callPut($resourceUri, $updateImageData);
        $this->_checkErrorMessagesInResponse($restResponse, 'Requested store is invalid');
    }

    /**
     * Test list images
     *
     * @magentoDataFixture Api2/Catalog/Products/Images/_fixture/product_simple.php
     * @resourceOperation product_image::multiget
     */
    public function testList()
    {
        $imageData = require dirname(__FILE__) . '/_fixture/Backend/ImageData.php';

        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        $fileNames = array();
        $fileFixtures = array();
        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('Mage_Catalog_Model_Product')->setStoreId(0)->load($product->getId());
        for ($i=1; $i<=3; $i++) {
            list($fileNames[$i], $fileFixtures[$i]) = $this->_getImageFixture();
            $this->_addImage($product, $fileFixtures[$i], $imageData['data_set_' . $i]);
        }

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
     * @magentoDataFixture Api2/Catalog/Products/Images/_fixture/product_simple.php
     * @resourceOperation product_image::get
     */
    public function testGet()
    {
        $imageData = require dirname(__FILE__) . '/_fixture/Backend/ImageData.php';
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
     * @magentoDataFixture Api2/Catalog/Products/Images/_fixture/product_simple.php
     * @resourceOperation product_image::delete
     */
    public function testDelete()
    {
        $imageData = require dirname(__FILE__) . '/_fixture/Backend/ImageData.php';
        $imageData = $imageData['data_set_1'];

        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        list($file, $fileFixture) = $this->_getImageFixture();
        $imageId = $this->_addImage($product, $fileFixture, $imageData);

        $restResponse = $this->callDelete('products/' . $product->getId() . '/images/' . $imageId);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('Mage_Catalog_Model_Product')->setStoreId(0)->load($product->getId());
        self::setFixture('product_simple', $product);

        $gallery = $product->getData('media_gallery');
        $this->assertEmpty($gallery['images']);
    }

    /**
     * Test image delete with invalid store
     *
     * @magentoDataFixture Api2/Catalog/Products/Images/_fixture/product_simple.php
     * @resourceOperation product_image::delete
     */
    public function testDeleteWithInvalidStoreId()
    {
        $imageData = require dirname(__FILE__) . '/_fixture/Backend/ImageData.php';
        $imageData = $imageData['data_set_1'];

        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        list($file, $fileFixture) = $this->_getImageFixture();
        $imageId = $this->_addImage($product, $fileFixture, $imageData);

        $resourceUri = 'products/' . $product->getId() . '/images/' . $imageId . '/store/invalidId';
        $restResponse = $this->callDelete($resourceUri);
        $this->_checkErrorMessagesInResponse($restResponse, 'Requested store is invalid');
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
     * @magentoDataFixture Api2/Catalog/Products/Images/_fixture/product_simple_on_new_store.php
     * @resourceOperation product_image::create
     */
    public function testPostWithStore()
    {
        $imageData = require dirname(__FILE__) . '/_fixture/Backend/ImageData.php';
        $imageData = $imageData['full_create'];
        $pathPrefix = '/' . substr($imageData['file_name'], 0, 1) . '/' . substr($imageData['file_name'], 1, 1) . '/';
        $file = $pathPrefix . $imageData['file_name'] . '.jpeg';

        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        /* @var $product Mage_Core_Model_Store */
        $store = self::getFixture('store');
        $resourceUri = 'products/' . $product->getId() . '/images/store/' . $store->getId();
        $restResponse = $this->callPost($resourceUri, $imageData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        // check image data on defined store
        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('Mage_Catalog_Model_Product')->setStoreId($store->getId())->load($product->getId());
        unset($imageData['file_content']);
        $createdImageData = $this->_getProductImageData($product, $file);
        $this->_checkImageData($imageData, $createdImageData);

        // check image data on default store, it should be empty
        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('Mage_Catalog_Model_Product')->load($product->getId());
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
     * @magentoDataFixture Api2/Catalog/Products/Images/_fixture/product_simple_on_new_store.php
     * @resourceOperation product_image::get
     */
    public function testGetWithStore()
    {
        $imageData = require dirname(__FILE__) . '/_fixture/Backend/ImageData.php';
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
        $product = Mage::getModel('Mage_Catalog_Model_Product')->setStoreId($store->getId())->load($product->getId());
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
     *
     * @resourceOperation product_image::multiget
     */
    public function testGetCollectionWithInvalidProduct()
    {
        $resourceUri = 'products/12099999/images/';
        $restResponse = $this->callGet($resourceUri);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }

    /**
     * Test image get
     *
     * @magentoDataFixture Api2/Catalog/Products/Images/_fixture/product_simple.php
     * @resourceOperation product_image::get
     */
    public function testGetWithInvalidStore()
    {
        $imageData = require dirname(__FILE__) . '/_fixture/Backend/ImageData.php';
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
     * @magentoDataFixture Catalog/Category/category_on_new_store.php
     * @magentoDataFixture Api2/Catalog/Products/Images/_fixture/product_simple.php
     * @resourceOperation product_image::get
     */
    public function testGetWithNotAssignedStore()
    {
        $imageData = require dirname(__FILE__) . '/_fixture/Backend/ImageData.php';
        $imageData = $imageData['data_set_1'];

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
        $product = Mage::getModel('Mage_Catalog_Model_Product')->setStoreId((int) $store)->load($product->getId());

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
        $fileFixture = dirname(__FILE__) . '/_fixture/' . $fileName;
        $this->_getIoAdapter()->cp(dirname(__FILE__) . '/_fixture/product.jpg', $fileFixture);

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
        $this->assertArrayHasKey('label', $data, '"Label" attribute is not exists in image data');
        $this->assertArrayHasKey('position', $data, '"Position" attribute is not exists in image data');
        $this->assertArrayHasKey('exclude', $data, '"Exclude" attribute is not exists in image data');
        $this->assertArrayHasKey('types', $data, '"Types" attribute is not exists in image data');
        if (array_key_exists('url', $data)) {
            if (array_key_exists('file', $expectedData)) {
                $this->assertContains($expectedData['file'], $data['url'], 'Image has wrong "url"');
            } elseif (array_key_exists('file_name', $expectedData)) {
                $this->assertContains($expectedData['file_name'], $data['url'], 'Image has wrong "url"');
            }
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
