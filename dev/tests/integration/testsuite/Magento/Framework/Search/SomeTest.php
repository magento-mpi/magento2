<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search;

use Magento\Framework\App\Resource;

/**
 * Sample test
 * TODO: need remove
 */
class SomeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $config;

    /**
     * @var Resource
     */
    private $appResource;

    /**
     * @var \Magento\Indexer\Model\Indexer[]
     */
    private $indexerList;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Flat\State
     */
    private $state;

    public function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $this->config = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        $this->appResource = $objectManager->get('Magento\Framework\App\Resource');
        $this->indexerList = $objectManager->get('\Magento\Indexer\Model\Indexer\CollectionFactory')
            ->create()
            ->getItems();
        $this->state = $objectManager->get('Magento\Catalog\Model\Indexer\Product\Flat\State');
    }

    /**
     * Sample test
     *
     * _magentoDbIsolatio enabled
     * _magentoAppIsolatio enabled
     * _magentoConfigFixtur current_store catalog/search/engine Magento\CatalogSearch\Model\Resource\Fulltext\Engine
     * _magentoConfigFixtur current_store catalog/frontend/flat_catalog_product 1
     * _magentoConfigFixtur current_store catalog/search/search_type 2
     *
     * _magentoDataFixtur Magento/Framework/Search/_files/products.php
     */
    public function testSome()
    {
        foreach ($this->indexerList as $indexer) {
            $indexer->reindexAll();
        }

        /** @var \Magento\Framework\DB\Adapter\AdapterInterface $connection */
        $connection = $this->appResource->getConnection(Resource::DEFAULT_READ_RESOURCE);
    }
}
