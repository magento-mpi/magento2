<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql;

use Magento\TestFramework\Helper\Bootstrap;

class AdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Search\Adapter\Mysql\Adapter
     */
    private $adapter;

    /**
     * @var \Magento\Framework\Search\RequestFactory
     */
    private $requestFactory;

    /**
     * @var \Magento\Framework\ObjectManager
     */
    private $objectManager;

    protected function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();

        /** @var \Magento\Framework\Search\Request\Config\Converter $converter */
        $converter = $this->objectManager->create('Magento\Framework\Search\Request\Config\Converter');

        $document = new \DOMDocument();
        $document->load(__DIR__ . '/../../_files/requests.xml');
        $requestConfig = $converter->convert($document);

        /** @var \Magento\Framework\Search\Request\Config $config */
        $config = $this->objectManager->create('Magento\Framework\Search\Request\Config');
        $config->merge($requestConfig);

        /** @var \Magento\Framework\Search\RequestFactory $requestFactory */
        $this->requestFactory = $this->objectManager->create(
            'Magento\Framework\Search\RequestFactory',
            ['config' => $config]
        );

        $this->adapter = $this->objectManager->create('Magento\Framework\Search\Adapter\Mysql\Adapter');
    }

    /**
     * Sample test
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store catalog/search/engine Magento\CatalogSearch\Model\Resource\Fulltext\Engine
     * @magentoConfigFixture current_store catalog/search/search_type 2
     * @magentoDataFixture Magento/Framework/Search/_files/products.php
     */
    public function testMatchQuery()
    {
        $bindValues = [
            '%request.title%' => 'socks',
        ];
        $requestName = 'one_match';

        $queryResponse = $this->executeQuery($requestName, $bindValues);
        $this->assertEquals(1, $queryResponse->count());
    }

    /**
     * Sample test
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store catalog/search/engine Magento\CatalogSearch\Model\Resource\Fulltext\Engine
     * @magentoConfigFixture current_store catalog/search/search_type 2
     * @magentoDataFixture Magento/Framework/Search/_files/products.php
     */
    public function testMatchQueryFilters()
    {
        $bindValues = [
            '%request.title%' => 'socks',
            '%pidm_from%' => 1,
            '%pidm_to%' => 3,
            '%pidsh%' => 4
        ];
        $requestName = 'one_match_filters';

        $queryResponse = $this->executeQuery($requestName, $bindValues);
        $this->assertEquals(1, $queryResponse->count());
    }

    /**
     * Range filter test with all fields filled
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store catalog/search/engine Magento\CatalogSearch\Model\Resource\Fulltext\Engine
     * @magentoConfigFixture current_store catalog/search/search_type 2
     * @magentoDataFixture Magento/Framework/Search/_files/products.php
     */
    public function testRangeFilterWithAllFields()
    {
        $bindValues = [
            '%request.product_id.from%' => 1,
            '%request.product_id.to%' => 3,
        ];
        $requestName = 'range_filter';

        $queryResponse = $this->executeQuery($requestName, $bindValues);
        $this->assertEquals(2, $queryResponse->count());
    }

    /**
     * Range filter test with all fields filled
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store catalog/search/engine Magento\CatalogSearch\Model\Resource\Fulltext\Engine
     * @magentoConfigFixture current_store catalog/search/search_type 2
     * @magentoDataFixture Magento/Framework/Search/_files/products.php
     */
    public function testRangeFilterWithoutFromField()
    {
        $bindValues = [
            '%request.product_id.to%' => 4,
        ];
        $requestName = 'range_filter_without_from_field';

        $queryResponse = $this->executeQuery($requestName, $bindValues);
        $this->assertEquals(3, $queryResponse->count());
    }

    /**
     * Range filter test with all fields filled
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store catalog/search/engine Magento\CatalogSearch\Model\Resource\Fulltext\Engine
     * @magentoConfigFixture current_store catalog/search/search_type 2
     * @magentoDataFixture Magento/Framework/Search/_files/products.php
     */
    public function testRangeFilterWithoutToField()
    {
        $bindValues = [
            '%request.product_id.from%' => 2,
        ];
        $requestName = 'range_filter_without_to_field';

        $queryResponse = $this->executeQuery($requestName, $bindValues);
        $this->assertEquals(4, $queryResponse->count());
    }

    /**
     * Term filter test
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store catalog/search/engine Magento\CatalogSearch\Model\Resource\Fulltext\Engine
     * @magentoConfigFixture current_store catalog/search/search_type 2
     * @magentoDataFixture Magento/Framework/Search/_files/products.php
     */
    public function testTermFilter()
    {
        $id = 4;

        $bindValues = [
            '%request.product_id%' => $id,
        ];
        $requestName = 'term_filter';

        $queryResponse = $this->executeQuery($requestName, $bindValues);
        $this->assertEquals(1, $queryResponse->count());
        $this->assertEquals($id, $queryResponse->getIterator()->offsetGet(0)->getId());
    }

    /**
     * Bool filter test
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store catalog/search/engine Magento\CatalogSearch\Model\Resource\Fulltext\Engine
     * @magentoConfigFixture current_store catalog/search/search_type 2
     * @magentoDataFixture Magento/Framework/Search/_files/products.php
     */
    public function testBoolFilter()
    {
        $expectedIds = [2, 3];
        $bindValues = [
            '%request.must.range_filter1.from%' => 1,
            '%request.must.range_filter1.to%' => 6,
            '%request.should.term_filter1%' => 1,
            '%request.should.term_filter2%' => 2,
            '%request.should.term_filter3%' => 3,
            '%request.should.term_filter4%' => 4,
            '%request.not.term_filter1%' => 1,
            '%request.not.term_filter2%' => 4,
        ];
        $requestName = 'bool_filter';

        $queryResponse = $this->executeQuery($requestName, $bindValues);
        $this->assertEquals(count($expectedIds), $queryResponse->count());
        $actualIds = [];
        foreach ($queryResponse as $document) {
            /** @var \Magento\Framework\Search\Document $document */
            $actualIds[] = $document->getId();
        }
        $this->assertEquals($expectedIds, $actualIds);
    }

    /**
     * Test bool filter with nested negative bool filter
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store catalog/search/engine Magento\CatalogSearch\Model\Resource\Fulltext\Engine
     * @magentoConfigFixture current_store catalog/search/search_type 2
     * @magentoDataFixture Magento/Framework/Search/_files/products.php
     */
    public function testBoolFilterWithNestedNegativeBoolFilter()
    {
        $expectedIds = [1];
        $bindValues = [
            '%request.not_range_filter.from%' => 2,
            '%request.not_range_filter.to%' => 5,
            '%request.nested_not_term_filter%' => 1,
        ];
        $requestName = 'bool_filter_with_nested_bool_filter';

        $queryResponse = $this->executeQuery($requestName, $bindValues);
        $this->assertEquals(count($expectedIds), $queryResponse->count());
        $actualIds = [];
        foreach ($queryResponse as $document) {
            /** @var \Magento\Framework\Search\Document $document */
            $actualIds[] = $document->getId();
        }
        $this->assertEquals($expectedIds, $actualIds);
    }

    /**
     * Test range inside nested negative bool filter
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store catalog/search/engine Magento\CatalogSearch\Model\Resource\Fulltext\Engine
     * @magentoConfigFixture current_store catalog/search/search_type 2
     * @magentoDataFixture Magento/Framework/Search/_files/products.php
     */
    public function testBoolFilterWithNestedRangeInNegativeBoolFilter()
    {
        $expectedIds = [5, 4, 1];
        $bindValues = [
            '%request.nested_must_range_filter.from%' => 2,
            '%request.nested_must_range_filter.to%' => 4,
        ];
        $requestName = 'bool_filter_with_range_in_nested_negative_filter';

        $queryResponse = $this->executeQuery($requestName, $bindValues);
        $this->assertEquals(count($expectedIds), $queryResponse->count());
        $actualIds = [];
        foreach ($queryResponse as $document) {
            /** @var \Magento\Framework\Search\Document $document */
            $actualIds[] = $document->getId();
        }
        $this->assertEquals($expectedIds, $actualIds);
    }

    /**
     * Sample Advanced search request test
     *
     * @dataProvider advancedSearchDataProvider
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store catalog/search/engine Magento\CatalogSearch\Model\Resource\Fulltext\Engine
     * @magentoConfigFixture current_store catalog/search/search_type 2
     * @magentoDataFixture Magento/Framework/Search/_files/products.php
     */
    public function testSimpleAdvancedSearch($bindValues, $expectedRecorsCount)
    {
        $requestName = 'advanced_search_test';

        $queryResponse = $this->executeQuery($requestName, $bindValues);
        $this->assertEquals($expectedRecorsCount, $queryResponse->count());
    }

    /**
     * @return array
     */
    public function advancedSearchDataProvider()
    {
        return array(
            [
                [
                    '%request.name%' => 'white',
                    '%request.description%' => 'shorts',
                    '%request.store_id%' => '1',
                    '%request.from_product_id%' => '3',
                    '%request.to_product_id%' => '4',
                ],
                0 // Record is not in filter range
            ],
            [
                [
                    '%request.name%' => 'white',
                    '%request.description%' => 'shorts',
                    '%request.store_id%' => '1',
                    '%request.from_product_id%' => '1',
                    '%request.to_product_id%' => '4',
                ],
                1 // One record is expected
            ],
            [
                [
                    '%request.name%' => 'white',
                    '%request.description%' => 'shorts',
                    '%request.store_id%' => '5',
                    '%request.from_product_id%' => '1',
                    '%request.to_product_id%' => '4',
                ],
                0 // store_id filter is invalid
            ],
            [
                [
                    '%request.name%' => 'black',
                    '%request.description%' => 'tshirts',
                    '%request.store_id%' => '1',
                    '%request.from_product_id%' => '1',
                    '%request.to_product_id%' => '5',
                ],
                0 // Non existing search terms
            ],
        );
    }

    private function executeQuery($requestName, $bindValues)
    {
        $this->reindexAll();

        /** @var \Magento\Framework\Search\Request $queryRequest */
        $queryRequest = $this->requestFactory->create($requestName, $bindValues);

        $queryResponse = $this->adapter->query($queryRequest);

        return $queryResponse;
    }

    private function reindexAll()
    {
        /** @var \Magento\Indexer\Model\Indexer[] $indexerList */
        $indexerList = $this->objectManager->get('\Magento\Indexer\Model\Indexer\CollectionFactory')
            ->create()
            ->getItems();

        foreach ($indexerList as $indexer) {
            $indexer->reindexAll();
        }
    }
}
