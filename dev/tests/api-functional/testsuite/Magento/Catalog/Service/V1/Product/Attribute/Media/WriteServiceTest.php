<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute\Media;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;
use Magento\TestFramework\Helper\Bootstrap;

class WriteServiceTest extends WebapiAbstract
{
    /**
     * Default create service request information (product with SKU 'simple' is used)
     *
     * @var array
     */
    protected $createServiceInfo;

    /**
     * Default update service request information (product with SKU 'simple' is used)
     *
     * @var array
     */
    protected $updateServiceInfo;

    /**
     * Default delete service request information (product with SKU 'simple' is used)
     *
     * @var array
     */
    protected $deleteServiceInfo;

    /**
     * @var string
     */
    protected $testImagePath;

    protected function setUp()
    {
        $this->createServiceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/products/simple/media',
                'httpMethod' => RestConfig::HTTP_METHOD_POST,
            ),
            'soap' => array(
                'service' => 'catalogProductAttributeMediaWriteServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'catalogProductAttributeMediaWriteServiceV1Create',
            ),
        );
        $this->updateServiceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/products/simple/media',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT,
            ),
            'soap' => array(
                'service' => 'catalogProductAttributeMediaWriteServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'catalogProductAttributeMediaWriteServiceV1Update',
            ),
        );
        $this->deleteServiceInfo = array(
            'rest' => array(
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE,
            ),
            'soap' => array(
                'service' => 'catalogProductAttributeMediaWriteServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'catalogProductAttributeMediaWriteServiceV1Delete',
            ),
        );
        $this->testImagePath = __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'test_image.jpg';
    }

    /**
     * Retrieve product that was updated by test
     *
     * @return \Magento\Catalog\Model\Product
     */
    protected function getTargetSimpleProduct()
    {
        $objectManager = Bootstrap::getObjectManager();
        return $objectManager->get('\Magento\Catalog\Model\ProductFactory')->create()->load(1);

    }

    /**
     * Retrieve target product image ID
     *
     * Target product must have single image if this function is used
     *
     * @return \Magento\Catalog\Model\Product
     */
    protected function getTargetGalleryEntryId()
    {
        $mediaGallery = $this->getTargetSimpleProduct()->getData('media_gallery');
        return $mediaGallery['images'][0]['value_id'];
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testCreate()
    {
        $requestData = array(
            'productSku' => 'simple',
            'entry' => array(
                'id' => null,
                'store_id' => null,
                'label' => 'Image Text',
                'position' => 1,
                'types' => array('image'),
                'disabled' => false,
            ),
            'entryContent' => array(
                'data' => base64_encode(file_get_contents($this->testImagePath)),
                'mime_type' => 'image/jpeg',
                'name' => 'test_image',
            ),
            // Store ID is not provided so the default one must be used
        );

        $actualResult = $this->_webApiCall($this->createServiceInfo, $requestData);
        $targetProduct = $this->getTargetSimpleProduct();
        $mediaGallery = $targetProduct->getData('media_gallery');

        $this->assertCount(1, $mediaGallery['images']);
        $updatedImage = $mediaGallery['images'][0];
        $this->assertEquals($actualResult, $updatedImage['value_id']);
        $this->assertEquals('Image Text', $updatedImage['label']);
        $this->assertEquals(1, $updatedImage['position']);
        $this->assertEquals(0, $updatedImage['disabled']);
        $this->assertEquals('Image Text', $updatedImage['label_default']);
        $this->assertEquals(1, $updatedImage['position_default']);
        $this->assertEquals(0, $updatedImage['disabled_default']);
        $this->assertStringStartsWith('/t/e/test_image', $updatedImage['file']);
        $this->assertEquals($updatedImage['file'], $targetProduct->getData('image'));
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testCreateWithNotDefaultStoreId()
    {
        $requestData = array(
            'productSku' => 'simple',
            'entry' => array(
                'id' => null,
                'store_id' => null,
                'label' => 'Image Text',
                'position' => 1,
                'types' => array('image'),
                'disabled' => false,
            ),
            'entryContent' => array(
                'data' => base64_encode(file_get_contents($this->testImagePath)),
                'mime_type' => 'image/jpeg',
                'name' => 'test_image',
            ),
            'storeId' => 1,
        );

        $actualResult = $this->_webApiCall($this->createServiceInfo, $requestData);
        $targetProduct = $this->getTargetSimpleProduct();
        $mediaGallery = $targetProduct->getData('media_gallery');
        $this->assertCount(1, $mediaGallery['images']);
        $updatedImage = $mediaGallery['images'][0];
        // Values for not default store view were provided
        $this->assertEquals('Image Text', $updatedImage['label']);
        $this->assertEquals($actualResult, $updatedImage['value_id']);
        $this->assertEquals(1, $updatedImage['position']);
        $this->assertEquals(0, $updatedImage['disabled']);
        $this->assertStringStartsWith('/t/e/test_image', $updatedImage['file']);
        $this->assertEquals($updatedImage['file'], $targetProduct->getData('image'));
        // No values for default store view were provided
        $this->assertNull($updatedImage['label_default']);
        $this->assertNull($updatedImage['position_default']);
        $this->assertNull($updatedImage['disabled_default']);

    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_with_image.php
     */
    public function testUpdate()
    {
        $requestData = array(
            'productSku' => 'simple',
            'entry' => array(
                'id' => $this->getTargetGalleryEntryId(),
                'store_id' => null,
                'label' => 'Updated Image Text',
                'position' => 10,
                'types' => array('thumbnail'),
                'disabled' => true,
            ),
            // Store ID is not provided so the default one must be used
        );

        $this->assertTrue($this->_webApiCall($this->updateServiceInfo, $requestData));

        $targetProduct = $this->getTargetSimpleProduct();
        $this->assertEquals('/m/a/magento_image.jpg', $targetProduct->getData('thumbnail'));
        $this->assertNull($targetProduct->getData('image'));
        $this->assertNull($targetProduct->getData('small_image'));
        $mediaGallery = $targetProduct->getData('media_gallery');
        $this->assertCount(1, $mediaGallery['images']);
        $updatedImage = $mediaGallery['images'][0];
        $this->assertEquals('Updated Image Text', $updatedImage['label']);
        $this->assertEquals('/m/a/magento_image.jpg', $updatedImage['file']);
        $this->assertEquals(10, $updatedImage['position']);
        $this->assertEquals(1, $updatedImage['disabled']);
        $this->assertEquals('Updated Image Text', $updatedImage['label_default']);
        $this->assertEquals(10, $updatedImage['position_default']);
        $this->assertEquals(1, $updatedImage['disabled_default']);
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_with_image.php
     */
    public function testUpdateWithNotDefaultStoreId()
    {
        $requestData = array(
            'productSku' => 'simple',
            'entry' => array(
                'id' => $this->getTargetGalleryEntryId(),
                'store_id' => null,
                'label' => 'Updated Image Text',
                'position' => 10,
                'types' => array('thumbnail'),
                'disabled' => true,
            ),
            'storeId' => 1,
        );

        $this->assertTrue($this->_webApiCall($this->updateServiceInfo, $requestData));

        $targetProduct = $this->getTargetSimpleProduct();
        $this->assertEquals('/m/a/magento_image.jpg', $targetProduct->getData('thumbnail'));
        $this->assertNull($targetProduct->getData('image'));
        $this->assertNull($targetProduct->getData('small_image'));
        $mediaGallery = $targetProduct->getData('media_gallery');
        $this->assertCount(1, $mediaGallery['images']);
        $updatedImage = $mediaGallery['images'][0];
        // Not default store view values were updated
        $this->assertEquals('Updated Image Text', $updatedImage['label']);
        $this->assertEquals('/m/a/magento_image.jpg', $updatedImage['file']);
        $this->assertEquals(10, $updatedImage['position']);
        $this->assertEquals(1, $updatedImage['disabled']);
        // Default store view values were not updated
        $this->assertEquals('Image Alt Text', $updatedImage['label_default']);
        $this->assertEquals(1, $updatedImage['position_default']);
        $this->assertEquals(0, $updatedImage['disabled_default']);
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_with_image.php
     */
    public function testDelete()
    {
        $entryId = $this->getTargetGalleryEntryId();
        $this->deleteServiceInfo['rest']['resourcePath'] = "/V1/products/simple/media/{$entryId}";
        $requestData = array(
            'productSku' => 'simple',
            'entryId' => $this->getTargetGalleryEntryId(),
        );

        $this->assertTrue($this->_webApiCall($this->deleteServiceInfo, $requestData));
        $targetProduct = $this->getTargetSimpleProduct();
        $mediaGallery = $targetProduct->getData('media_gallery');
        $this->assertCount(0, $mediaGallery['images']);
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     * @expectedException \Exception
     * @expectedExceptionMessage There is no store with provided ID.
     */
    public function testCreateThrowsExceptionIfThereIsNoStoreWithProvidedStoreId()
    {
        $requestData = array(
            'productSku' => 'simple',
            'entry' => array(
                'id' => null,
                'store_id' => null,
                'label' => 'Image Text',
                'position' => 1,
                'types' => array('image'),
                'disabled' => false,
            ),
            'storeId' => 9999, // target store view does not exist
            'entryContent' => array(
                'data' => base64_encode(file_get_contents($this->testImagePath)),
                'mime_type' => 'image/jpeg',
                'name' => 'test_image',
            ),
        );

        $this->_webApiCall($this->createServiceInfo, $requestData);
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     * @expectedException \Exception
     * @expectedExceptionMessage The image content must be valid base64 encoded data.
     */
    public function testCreateThrowsExceptionIfProvidedContentIsNotBase64Encoded()
    {
        $encodedContent = 'not_a_base64_encoded_content';
        $requestData = array(
            'productSku' => 'simple',
            'entry' => array(
                'id' => null,
                'store_id' => null,
                'label' => 'Image Text',
                'position' => 1,
                'disabled' => false,
                'types' => array('image'),
            ),
            'entryContent' => array(
                'data' => $encodedContent,
                'mime_type' => 'image/jpeg',
                'name' => 'test_image',
            ),
            'storeId' => 0,
        );

        $this->_webApiCall($this->createServiceInfo, $requestData);
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     * @expectedException \Exception
     * @expectedExceptionMessage The image content must be valid base64 encoded data.
     */
    public function testCreateThrowsExceptionIfProvidedContentIsNotAnImage()
    {
        $encodedContent = base64_encode('not_an_image');
        $requestData = array(
            'productSku' => 'simple',
            'entry' => array(
                'id' => null,
                'store_id' => null,
                'disabled' => false,
                'label' => 'Image Text',
                'position' => 1,
                'types' => array('image'),
            ),
            'entryContent' => array(
                'data' => $encodedContent,
                'mime_type' => 'image/jpeg',
                'name' => 'test_image',
            ),
            'storeId' => 0,
        );

        $this->_webApiCall($this->createServiceInfo, $requestData);
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     * @expectedException \Exception
     * @expectedExceptionMessage The image MIME type is not valid or not supported.
     */
    public function testCreateThrowsExceptionIfProvidedImageHasWrongMimeType()
    {
        $encodedContent = base64_encode(file_get_contents($this->testImagePath));
        $requestData = array(
            'entry' => array(
                'id' => null,
                'store_id' => null,
                'label' => 'Image Text',
                'position' => 1,
                'types' => array('image'),
                'disabled' => false,
            ),
            'productSku' => 'simple',
            'entryContent' => array(
                'data' => $encodedContent,
                'mime_type' => 'wrong_mime_type',
                'name' => 'test_image',
            ),
            'storeId' => 0,
        );

        $this->_webApiCall($this->createServiceInfo, $requestData);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage There is no product with provided SKU
     */
    public function testCreateThrowsExceptionIfTargetProductDoesNotExist()
    {
        $this->createServiceInfo['rest']['resourcePath'] = '/V1/products/wrong_product_sku/media';
        $requestData = array(
            'productSku' => 'wrong_product_sku',
            'entry' => array(
                'id' => null,
                'store_id' => null,
                'position' => 1,
                'label' => 'Image Text',
                'types' => array('image'),
                'disabled' => false,
            ),
            'entryContent' => array(
                'data' => base64_encode(file_get_contents($this->testImagePath)),
                'mime_type' => 'image/jpeg',
                'name' => 'test_image',
            ),
            'storeId' => 0,
        );

        $this->_webApiCall($this->createServiceInfo, $requestData);
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     * @expectedException \Exception
     * @expectedExceptionMessage Provided image name contains forbidden characters.
     */
    public function testCreateThrowsExceptionIfProvidedImageNameContainsForbiddenCharacters()
    {
        $requestData = array(
            'productSku' => 'simple',
            'entry' => array(
                'id' => null,
                'store_id' => null,
                'label' => 'Image Text',
                'position' => 1,
                'types' => array('image'),
                'disabled' => false,
            ),
            'entryContent' => array(
                'data' => base64_encode(file_get_contents($this->testImagePath)),
                'mime_type' => 'image/jpeg',
                'name' => 'test/\\{}|:"<>', // Cannot contain \ / : * ? " < > |
            ),
            'storeId' => 0,
        );

        $this->_webApiCall($this->createServiceInfo, $requestData);
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_with_image.php
     * @expectedException \Exception
     * @expectedExceptionMessage There is no store with provided ID.
     */
    public function testUpdateIfThereIsNoStoreWithProvidedStoreId()
    {
        $requestData = array(
            'productSku' => 'simple',
            'entry' => array(
                'id' => $this->getTargetGalleryEntryId(),
                'store_id' => null,
                'label' => 'Updated Image Text',
                'position' => 10,
                'types' => array('thumbnail'),
                'disabled' => true,
            ),
            'storeId' => 9999, // target store view does not exist
        );

        $this->_webApiCall($this->updateServiceInfo, $requestData);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage There is no product with provided SKU
     */
    public function testUpdateThrowsExceptionIfTargetProductDoesNotExist()
    {
        $this->updateServiceInfo['rest']['resourcePath'] = '/V1/products/wrong_product_sku/media';
        $requestData = array(
            'productSku' => 'wrong_product_sku',
            'entry' => array(
                'id' => 9999,
                'store_id' => null,
                'label' => 'Updated Image Text',
                'position' => 1,
                'types' => array('thumbnail'),
                'disabled' => true,
            ),
            'storeId' => 0,
        );

        $this->_webApiCall($this->updateServiceInfo, $requestData);
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_with_image.php
     * @expectedException \Exception
     * @expectedExceptionMessage There is no image with provided ID.
     */
    public function testUpdateThrowsExceptionIfThereIsNoImageWithGivenId()
    {
        $requestData = array(
            'productSku' => 'simple',
            'entry' => array(
                'id' => 9999,
                'store_id' => null,
                'label' => 'Updated Image Text',
                'position' => 1,
                'types' => array('thumbnail'),
                'disabled' => true,
            ),
            'storeId' => 0,
        );

        $this->_webApiCall($this->updateServiceInfo, $requestData);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage There is no product with provided SKU
     */
    public function testDeleteThrowsExceptionIfTargetProductDoesNotExist()
    {
        $this->deleteServiceInfo['rest']['resourcePath'] = '/V1/products/wrong_product_sku/media/9999';
        $requestData = array(
            'productSku' => 'wrong_product_sku',
            'entryId' => 9999,
        );

        $this->_webApiCall($this->deleteServiceInfo, $requestData);
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_with_image.php
     * @expectedException \Exception
     * @expectedExceptionMessage There is no image with provided ID.
     */
    public function testDeleteThrowsExceptionIfThereIsNoImageWithGivenId()
    {
        $this->deleteServiceInfo['rest']['resourcePath'] = '/V1/products/simple/media/9999';
        $requestData = array(
            'productSku' => 'simple',
            'entryId' => 9999,
        );

        $this->_webApiCall($this->deleteServiceInfo, $requestData);
    }
}
