<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Service\V1;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\GiftWrapping\Service\V1\WrappingRead as WrappingService;
use Magento\Webapi\Model\Rest\Config as RestConfig;

class WrappingReadTest extends WebapiAbstract
{
    const RESOURCE_PATH = '/V1/gift-wrappings';
    const SERVICE_NAME = 'giftWrappingWrappingReadV1';
    const SERVICE_VERSION = 'V1';

    /** @var \Magento\Framework\ObjectManager */
    private  $objectManager;

    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;

    /** @var FilterBuilder */
    private $filterBuilder;

    /** @var WrappingService */
    private $wrappingService;

    /**
     * Execute per test initialization.
     */
    public function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->wrappingService = $this->objectManager->create('Magento\GiftWrapping\Service\V1\WrappingRead');
        $this->searchCriteriaBuilder = $this->objectManager->create(
            'Magento\Framework\Api\SearchCriteriaBuilder'
        );
        $this->filterBuilder = $this->objectManager->create(
            'Magento\Framework\Api\FilterBuilder'
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
            'wrapping_id' => $data->getWrappingId(),
            'design' => $data->getDesign(),
            'status' => $data->getStatus(),
            'base_price' => $data->getBasePrice(),
            'image_name' => $data->getImageName(),
            'website_ids' => $data->getWebsiteIds()
        ];
        foreach ($expectedData as $key => $value) {
            $resultValue = isset($result[$key]) ? $result[$key] : null;
            $this->assertEquals($value, $resultValue, "Assertion for property {$key} failed");
        }
        $this->assertStringStartsWith('http', $result['image_url'], 'Image URL property is incorrect');
    }

    /**
     * @magentoApiDataFixture Magento/GiftWrapping/_files/wrappings.php
     */
    public function testSearch()
    {
        $result = $this->callSearch([]);
        $this->assertArrayHasKey('items', $result);
        $collection = $this->getWrappingCollection();
        $this->assertCount($collection->count(), $result['items']);
        self::deleteAllFixtures();
    }

    /**
     * Perform search call to API
     *
     * @param array $filters
     * @return array|bool|float|int|string
     */
    private function callSearch(array $filters)
    {
        $storeId = $this->objectManager->get('Magento\Framework\StoreManagerInterface')->getStore()->getId();
        $filters[] = $this->filterBuilder->setField('store_id')->setValue($storeId)->create();
        $this->searchCriteriaBuilder->addFilter($filters);
        $searchData = $this->searchCriteriaBuilder->create()->__toArray();
        $requestData = ['searchCriteria' => $searchData];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '?' . http_build_query($requestData),
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'search'
            ]
        ];
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

    private static function deleteAllFixtures()
    {
        $collection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\GiftWrapping\Model\Resource\Wrapping\Collection');
        foreach ($collection as $item) {
            /** @var \Magento\GiftWrapping\Model\Wrapping $item */
            $item->delete();
        }
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        self::deleteAllFixtures();
    }
}
