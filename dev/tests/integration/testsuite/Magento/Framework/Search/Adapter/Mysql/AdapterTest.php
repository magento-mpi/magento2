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
     * @var \Magento\Framework\Search\Request\Builder
     */
    private $requestBuilder;

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

        $this->requestBuilder = $this->objectManager->create(
            'Magento\Framework\Search\Request\Builder',
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
        $this->requestBuilder->bind('fulltext_search_query', 'socks');
        $this->requestBuilder->setRequestName('one_match');

        $queryResponse = $this->executeQuery();

        $this->assertEquals(1, $queryResponse->count());
    }

    private function executeQuery()
    {
        $this->reindexAll();

        /** @var \Magento\Framework\Search\Request $queryRequest */
        $queryRequest = $this->requestBuilder->create();

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
        $this->requestBuilder->bind('fulltext_search_query', 'socks');
        $this->requestBuilder->bind('pidm_from', 1);
        $this->requestBuilder->bind('pidm_to', 3);
        $this->requestBuilder->bind('pidsh', 4);
        $this->requestBuilder->setRequestName('one_match_filters');

        $queryResponse = $this->executeQuery();
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
        $this->requestBuilder->bind('range_filter_from', 1);
        $this->requestBuilder->bind('range_filter_to', 3);
        $this->requestBuilder->setRequestName('range_filter');

        $queryResponse = $this->executeQuery();
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
        $this->requestBuilder->bind('range_filter_to', 4);
        $this->requestBuilder->setRequestName('range_filter_without_from_field');

        $queryResponse = $this->executeQuery();
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
        $this->requestBuilder->bind('from', 2);
        $this->requestBuilder->setRequestName('range_filter_without_to_field');

        $queryResponse = $this->executeQuery();
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

        $this->requestBuilder->bind('request.product_id', $id);
        $this->requestBuilder->setRequestName('term_filter');

        $queryResponse = $this->executeQuery();
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
        /*
         * TODO: Remove test skipping after fixing issue
         */
        $this->markTestSkipped('Bool filter doesn\'t work correctly and we have issue in bug tracker');
        $expectedIds = [2, 3];

        $this->requestBuilder->bind('must_range_filter1', ['from' => 1, 'to' => 5]);
        $this->requestBuilder->bind('should_term_filter1', 1);
        $this->requestBuilder->bind('should_term_filter2', 2);
        $this->requestBuilder->bind('should_term_filter3', 3);
        $this->requestBuilder->bind('should_term_filter4', 4);
        $this->requestBuilder->bind('not_term_filter1', 1);
        $this->requestBuilder->bind('not_term_filter2', 4);
        $this->requestBuilder->bind('should_term_filter3', 3);
        $this->requestBuilder->setRequestName('bool_filter');

        $queryResponse = $this->executeQuery();
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
    public function testSimpleAdvancedSearch(
        $nameQuery,
        $descriptionQuery,
        $storeFilter,
        $rangeFilter,
        $expectedRecordsCount
    ) {
        $this->requestBuilder->bind('name_query', $nameQuery);
        $this->requestBuilder->bind('description_query', $descriptionQuery);
        $this->requestBuilder->bind('store_filter', $storeFilter);
        $this->requestBuilder->bind('request.from_product_id', $rangeFilter['from']);
        $this->requestBuilder->bind('request.to_product_id', $rangeFilter['to']);
        $this->requestBuilder->setRequestName('advanced_search_test');

        $queryResponse = $this->executeQuery();
        $this->assertEquals($expectedRecordsCount, $queryResponse->count());
    }

    /**
     * @return array
     */
    public function advancedSearchDataProvider()
    {
        return array(
            ['white', 'shorts', '1', ['from' => '3', 'to' => '4'], 0],
            ['white', 'shorts', '1', ['from' => '1', 'to' => '4'], 1],
            ['white', 'shorts', '5', ['from' => '1', 'to' => '4'], 0],
            ['black', 'tshirts', '1', ['from' => '1', 'to' => '5'], 0],
        );
    }
}
