<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Service\V1;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\GiftWrapping\Service\V1\WrappingWrite as WrappingService;
use Magento\GiftWrapping\Service\V1\WrappingRead as WrappingReadService;
use Magento\GiftWrapping\Service\V1\Data\WrappingBuilder;
use Magento\Webapi\Model\Rest\Config as RestConfig;
use Magento\Framework\Exception\NoSuchEntityException;

class WrappingWriteTest extends WebapiAbstract
{
    const RESOURCE_PATH = '/V1/gift-wrappings';
    const SERVICE_NAME = 'giftWrappingWrappingWriteV1';
    const SERVICE_VERSION = 'V1';

    /** @var \Magento\Framework\ObjectManager */
    private  $objectManager;

    /** @var WrappingBuilder */
    private $wrappingBuilder;

    /** @var WrappingService */
    private $wrappingService;

    /** @var WrappingReadService */
    private $wrappingReadService;

    /** @var string */
    private $testImagePath;

    /**
     * Execute per test initialization.
     */
    public function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->wrappingService = $this->objectManager->create('Magento\GiftWrapping\Service\V1\WrappingWrite');
        $this->wrappingReadService = $this->objectManager->create('Magento\GiftWrapping\Service\V1\WrappingRead');
        $this->wrappingBuilder = $this->objectManager->create('Magento\GiftWrapping\Service\V1\Data\WrappingBuilder');
        $this->testImagePath = __DIR__ . str_replace('/', DIRECTORY_SEPARATOR, '/_files/test_image.jpg');
    }

    public function testCreate()
    {
        $this->wrappingBuilder->setWebsiteIds([1]);
        $this->wrappingBuilder->setStatus(1);
        $this->wrappingBuilder->setDesign('New Wrapping');
        $this->wrappingBuilder->setBasePrice(10.0);
        $this->wrappingBuilder->setImageName('image.jpg');
        $this->wrappingBuilder->setImageBase64Content(base64_encode(file_get_contents($this->testImagePath)));
        /** @var \Magento\GiftWrapping\Service\V1\Data\Wrapping $dataObject */
        $dataObject = $this->wrappingBuilder->create();

        $wrappingId = $this->callCreate($dataObject);
        $this->assertNotNull($wrappingId);

        $actualDataObject = $this->wrappingReadService->get($wrappingId);

        $this->assertEquals($wrappingId, $actualDataObject->getWrappingId());
        $this->assertEquals($dataObject->getStatus(), $actualDataObject->getStatus());
        $this->assertEquals($dataObject->getBasePrice(), $actualDataObject->getBasePrice());
        $this->assertEquals($dataObject->getImageName(), $actualDataObject->getImageName());
        $this->assertEquals($dataObject->getDesign(), $actualDataObject->getDesign());
        $this->assertEquals($dataObject->getWebsiteIds(), $actualDataObject->getWebsiteIds());
        $this->assertStringStartsWith('http', $actualDataObject->getImageUrl(), 'Image URL property is incorrect');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Parameter id is not expected for this request
     */
    public function testCreateIdNotExpected()
    {
        $this->wrappingBuilder->setWebsiteIds([1]);
        $this->wrappingBuilder->setStatus(1);
        $this->wrappingBuilder->setDesign('New Wrapping');
        $this->wrappingBuilder->setBasePrice(10.0);
        $this->wrappingBuilder->setWrappingId(1);
        /** @var \Magento\GiftWrapping\Service\V1\Data\Wrapping $dataObject */
        $dataObject = $this->wrappingBuilder->create();
        $this->assertNull($this->callCreate($dataObject));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage The image content must be valid data.
     */
    public function testCreateImageValidationFailed()
    {
        $this->wrappingBuilder->setWebsiteIds([1]);
        $this->wrappingBuilder->setStatus(1);
        $this->wrappingBuilder->setDesign('New Wrapping');
        $this->wrappingBuilder->setBasePrice(10.0);
        $this->wrappingBuilder->setImageName('image.jpg');
        $this->wrappingBuilder->setImageBase64Content('invalid base64 content');
        /** @var \Magento\GiftWrapping\Service\V1\Data\Wrapping $dataObject */
        $dataObject = $this->wrappingBuilder->create();
        $this->assertNull($this->callCreate($dataObject));
    }

    /**
     * @magentoApiDataFixture Magento/GiftWrapping/_files/wrapping.php
     */
    public function testUpdate()
    {
        $wrapping = $this->getWrappingFixture();

        $this->wrappingBuilder->setStatus(0);
        $this->wrappingBuilder->setDesign('Changed Wrapping');
        $this->wrappingBuilder->setBasePrice(50.0);
        $this->wrappingBuilder->setImageName('image_updated.jpg');
        $this->wrappingBuilder->setImageBase64Content(base64_encode(file_get_contents($this->testImagePath)));
        /** @var \Magento\GiftWrapping\Service\V1\Data\Wrapping $dataObject */
        $dataObject = $this->wrappingBuilder->create();

        $wrappingId = $this->callUpdate($wrapping->getId(), $dataObject);
        $this->assertEquals($wrapping->getId(), $wrappingId);

        $actualDataObject = $this->wrappingReadService->get($wrappingId);
        $this->assertEquals($wrappingId, $actualDataObject->getWrappingId());
        $this->assertEquals($dataObject->getStatus(), $actualDataObject->getStatus());
        $this->assertEquals($dataObject->getBasePrice(), $actualDataObject->getBasePrice());
        $this->assertEquals($dataObject->getImageName(), $actualDataObject->getImageName());
        $this->assertEquals($dataObject->getDesign(), $actualDataObject->getDesign());
        $this->assertStringStartsWith('http', $actualDataObject->getImageUrl(), 'Image URL property is incorrect');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Gift Wrapping with ID
     */
    public function testUpdateNoSuchEntity()
    {
        $this->wrappingBuilder->setStatus(0);
        $this->wrappingBuilder->setDesign('Changed Wrapping');
        $this->wrappingBuilder->setBasePrice(50.0);
        $this->wrappingBuilder->setImageName('image_updated.jpg');
        $this->wrappingBuilder->setImageBase64Content(base64_encode(file_get_contents($this->testImagePath)));
        /** @var \Magento\GiftWrapping\Service\V1\Data\Wrapping $dataObject */
        $dataObject = $this->wrappingBuilder->create();
        $this->assertNull($this->callUpdate(-1, $dataObject));
    }

    /**
     * @magentoApiDataFixture Magento/GiftWrapping/_files/wrapping.php
     */
    public function testDelete()
    {
        $wrapping = $this->getWrappingFixture();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $wrapping->getId(),
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'delete'
            ]
        ];
        $requestData = ['id' => $wrapping->getId()];
        $result = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertTrue($result);

        try {
            $this->wrappingReadService->get($wrapping->getId());
            $this->fail("Gift Wrapping was not expected to be returned after being deleted.");
        } catch (NoSuchEntityException $e) {
            $this->assertStringStartsWith('Gift Wrapping with specified ID', $e->getMessage());
        }
    }

    /**
     * Perform create call to API
     *
     * @param Data\Wrapping $dataObject
     * @return array|bool|float|int|string
     */
    private function callCreate(Data\Wrapping $dataObject)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'create'
            ]
        ];

        $requestData = ['data' => $dataObject->__toArray()];
        return $this->_webApiCall($serviceInfo, $requestData);
    }

    /**
     * Perform update call to API
     *
     * @param int $id
     * @param Data\Wrapping $dataObject
     * @return array|bool|float|int|string
     */
    private function callUpdate($id, Data\Wrapping $dataObject)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $id,
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'update'
            ]
        ];

        $requestData = ['data' => $dataObject->__toArray(), 'id' => $id];
        return $this->_webApiCall($serviceInfo, $requestData);
    }

    /**
     * Return collection of wrapping items sorted by ID descending
     *
     * @return \Magento\GiftWrapping\Model\Resource\Wrapping\Collection
     */
    private function getWrappingCollection()
    {
        /** @var \Magento\GiftWrapping\Model\Resource\Wrapping\Collection $collection */
        $collection = $this->objectManager->create('Magento\GiftWrapping\Model\Resource\Wrapping\Collection');
        $collection->setOrder('wrapping_id');
        return $collection;
    }

    /**
     * Return last created wrapping fixture
     *
     * @return \Magento\GiftWrapping\Model\Wrapping
     */
    private function getWrappingFixture()
    {
        $collection = $this->getWrappingCollection();
        $collection->setPageSize(1);
        $collection->load();
        return $collection->fetchItem();
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        $collection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\GiftWrapping\Model\Resource\Wrapping\Collection');
        foreach ($collection as $item) {
            /** @var \Magento\GiftWrapping\Model\Wrapping $item */
            $item->delete();
        }
    }
}
