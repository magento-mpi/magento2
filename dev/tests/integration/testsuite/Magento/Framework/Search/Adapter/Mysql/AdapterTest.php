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
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store catalog/search/engine Magento\CatalogSearch\Model\Resource\Fulltext\Engine
     * @magentoConfigFixture current_store catalog/search/search_type 2
     * @magentoDataFixture Magento/Framework/Search/_files/products.php
     */
    public function testOneMatchQuery()
    {
        $bindValues = [
            '%request.title%' => 'socks',
        ];
        $requestName = 'one_match';

        $queryResponse = $this->executeQuery($requestName, $bindValues);
        $this->assertEquals(1, $queryResponse->count());
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
