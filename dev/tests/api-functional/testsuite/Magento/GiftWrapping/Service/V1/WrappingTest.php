<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Service\V1;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Framework\Service\V1\Data\FilterBuilder;
use Magento\Framework\Service\V1\Data\SearchCriteriaBuilder;
use Magento\GiftWrapping\Service\V1\Wrapping as WrappingService;
use Magento\GiftWrapping\Service\V1\Data\WrappingBuilder;
use Magento\GiftWrapping\Service\V1\Data\WrappingMapper;
use Magento\Webapi\Model\Rest\Config as RestConfig;
use Magento\Framework\Exception\NoSuchEntityException;
use PHPUnit_Framework_TestCase;

class GiftWrappingTest extends WebapiAbstract
{
    const RESOURCE_PATH = '/V1/gift-wrappings';
    const SERVICE_NAME = 'giftWrappingWrappingV1';
    const SERVICE_VERSION = 'V1';

    /** @var \Magento\Framework\ObjectManager */
    private  $objectManager;

    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;

    /** @var FilterBuilder */
    private $filterBuilder;

    /** @var WrappingBuilder */
    private $wrappingBuilder;

    /** @var WrappingMapper */
    private $wrappingMapper;

    /** @var WrappingService */
    private $wrappingService;

    /**
     * Execute per test initialization.
     */
    public function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $this->wrappingService = $this->objectManager->create('Magento\GiftWrapping\Service\V1\Wrapping');
        $this->wrappingBuilder = $this->objectManager->create('Magento\GiftWrapping\Service\V1\Data\WrappingBuilder');
        $this->wrappingMapper = $this->objectManager->create('Magento\GiftWrapping\Service\V1\Data\WrappingMapper');
        $this->searchCriteriaBuilder = $this->objectManager->create(
            'Magento\Framework\Service\V1\Data\SearchCriteriaBuilder'
        );
        $this->filterBuilder = $this->objectManager->create(
            'Magento\Framework\Service\V1\Data\FilterBuilder'
        );
    }

    /**
     * @magentoApiDataFixture Magento/GiftWrapping/_files/wrapping.php
     */
    public function testGet()
    {
        $wrapping = $this->getWrappingFixture();
        $data = $this->wrappingService->get($wrapping->getId());

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $wrapping->getId(),
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'get'
            ]
        ];
        $result = $this->_webApiCall($serviceInfo, ['id' => $wrapping->getId()]);
        $expectedData = [
            'wrapping_id' => $data->getId(),
            'design' => $data->getDesign(),
            'status' => $data->getStatus(),
            'base_price' => $data->getBasePrice(),
            'image' => $data->getImageName(),
            'website_ids' => $data->getWebsiteIds()
        ];
        foreach ($expectedData as $key => $value) {
            $resultValue = isset($result[$key]) ? $result[$key] : null;
            $this->assertEquals($value, $resultValue, "Assertion for property {$key} failed");
        }
        $this->assertStringStartsWith('http', $result['image_url'], 'Image URL property is incorrect');
    }

    public function testCreate()
    {
        $this->wrappingBuilder->setWebsiteIds([1]);
        $this->wrappingBuilder->setStatus(1);
        $this->wrappingBuilder->setDesign('New Wrapping');
        $this->wrappingBuilder->setBasePrice(10.0);
        $this->wrappingBuilder->setImageName('image.jpg');
        $this->wrappingBuilder->setImageBase64Content(base64_encode('image binary content'));
        /** @var \Magento\GiftWrapping\Service\V1\Data\Wrapping $dataObject */
        $dataObject = $this->wrappingBuilder->create();

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
        $wrappingId = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertNotNull($wrappingId);

        $actualDataObject = $this->wrappingService->get($wrappingId);
//print_r($actualDataObject);
        $this->assertEquals($wrappingId, $actualDataObject->getId());
        $this->assertEquals($dataObject->getStatus(), $actualDataObject->getStatus());
        $this->assertEquals($dataObject->getBasePrice(), $actualDataObject->getBasePrice());
        $this->assertEquals('image.jpg', $actualDataObject->getImageName());
        $this->assertEquals($dataObject->getDesign(), $actualDataObject->getDesign());
        $this->assertEquals($dataObject->getWebsiteIds(), $actualDataObject->getWebsiteIds());
        $this->assertStringStartsWith('http', $actualDataObject->getImageUrl(), 'Image URL property is incorrect');
    }

    /**
     * @magentoApiDataFixture Magento/GiftWrapping/_files/wrapping.php
     */
    public function testUpdate()
    {
        $wrapping = $this->getWrappingFixture();

        $this->wrappingBuilder->setId($wrapping->getId());
        $this->wrappingBuilder->setWebsiteIds([]);
        $this->wrappingBuilder->setStatus(0);
        $this->wrappingBuilder->setDesign('Changed Wrapping');
        $this->wrappingBuilder->setBasePrice(50.0);
        $this->wrappingBuilder->setImageName('image_updated.jpg');
        $this->wrappingBuilder->setImageBase64Content(base64_encode('image binary content changed'));
        /** @var \Magento\GiftWrapping\Service\V1\Data\Wrapping $dataObject */
        $dataObject = $this->wrappingBuilder->create();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $wrapping->getId(),
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'update'
            ]
        ];

        $requestData = ['data' => $dataObject->__toArray(), 'id' => $wrapping->getId()];
        $wrappingId = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertNotNull($wrappingId);
        $this->assertEquals($wrapping->getId(), $wrappingId);

        $actualDataObject = $this->wrappingService->get($wrappingId);
//print_r($actualDataObject);
        $this->assertEquals($wrappingId, $actualDataObject->getId());
        $this->assertEquals($dataObject->getStatus(), $actualDataObject->getStatus());
        $this->assertEquals($dataObject->getBasePrice(), $actualDataObject->getBasePrice());
        $this->assertEquals('image_updated.jpg', $actualDataObject->getImageName());
        $this->assertEquals($dataObject->getDesign(), $actualDataObject->getDesign());
        //$this->assertEquals($dataObject->getWebsiteIds(), $actualDataObject->getWebsiteIds());
        $this->assertStringStartsWith('http', $actualDataObject->getImageUrl(), 'Image URL property is incorrect');
    }

    /**
     * @magentoApiDataFixture Magento/GiftWrapping/_files/wrapping.php
     */
    public function testSearch()
    {
        $this->searchCriteriaBuilder->addFilter(
            [
                $this->filterBuilder
                    ->setField('status')
                    ->setValue(1)
                    ->create()
            ]
        );
        $searchData = $this->searchCriteriaBuilder->create()->__toArray();

        $requestData = ['searchCriteria' => $searchData];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '?' . http_build_query($requestData),
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'search'
            ]
        ];

        $result = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertArrayHasKey('items', $result);

        $collection = $this->getWrappingCollection();
        $collection->applyStatusFilter();

        $this->assertCount($collection->count(), $result['items']);
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
            $this->wrappingService->get($wrapping->getId());
            $this->fail("Gift Wrapping was not expected to be returned after being deleted.");
        } catch (NoSuchEntityException $e) {
            $this->assertStringStartsWith('Gift Wrapping with specified ID', $e->getMessage());
        }
    }

    /**
     * Return collection of wrapping items sorted by ID descending
     *
     * @return \Magento\GiftWrapping\Model\Resource\Wrapping\Collection
     */
    protected function getWrappingCollection()
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
    protected function getWrappingFixture()
    {
        $collection = $this->getWrappingCollection();
        $collection->setPageSize(1);
        $collection->load();
        return $collection->fetchItem();
    }

    public static function tearDownAfterClass()
    {

        parent::tearDownAfterClass(); // TODO: Change the autogenerated stub
    }
}
