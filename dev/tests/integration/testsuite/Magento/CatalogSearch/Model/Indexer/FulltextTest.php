<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\CatalogSearch\Model\Indexer;

/**
 * @magentoDataFixture Magento/CatalogSearch/_files/indexer_fulltext.php
 */
class FulltextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Indexer\Model\IndexerInterface
     */
    protected $indexer;

    /**
     * @var \Magento\CatalogSearch\Model\Resource\Fulltext\Engine
     */
    protected $engine;

    /**
     * @var \Magento\CatalogSearch\Model\Resource\Fulltext
     */
    protected $resourceFulltext;

    /**
     * @var \Magento\CatalogSearch\Model\Fulltext
     */
    protected $fulltext;

    /**
     * @var \Magento\Search\Model\QueryFactory
     */
    protected $queryFactory;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $productFirst;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $productSecond;

    protected function setUp()
    {
        /** @var \Magento\Indexer\Model\IndexerInterface indexer */
        $this->indexer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Indexer\Model\Indexer'
        );
        $this->indexer->load('catalogsearch_fulltext');

        $this->engine = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\CatalogSearch\Model\Resource\Fulltext\Engine'
        );

        $this->resourceFulltext = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\CatalogSearch\Model\Resource\Fulltext'
        );

        $this->fulltext = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\CatalogSearch\Model\Fulltext'
        );

        $this->queryFactory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Search\Model\QueryFactory'
        );

        $this->productFirst = $this->getProductBySku('fulltext-1');
        $this->productSecond = $this->getProductBySku('fulltext-2');
    }

    public function testReindexAll()
    {
        $this->engine->cleanIndex();

        $this->indexer->reindexAll();

        $products = $this->search('Simple Product First');
        $this->assertCount(1, $products);
        $this->assertEquals($this->productFirst->getId(), $products[0]->getId());

        $products = $this->search('Simple Product');
        $this->assertCount(2, $products);
        $this->assertEquals($this->productFirst->getId(), $products[0]->getId());
        $this->assertEquals($this->productSecond->getId(), $products[1]->getId());
    }

    /**
     * @depends testReindexAll
     */
    public function testReindexRowAfterEdit()
    {
        $this->productFirst->setData('name', 'Simple Product Third');
        $this->productFirst->save();

        $products = $this->search('Simple Product First');
        $this->assertCount(0, $products);

        $products = $this->search('Simple Product Third');
        $this->assertCount(1, $products);
        $this->assertEquals($this->productFirst->getId(), $products[0]->getId());

        $products = $this->search('Simple Product');
        $this->assertCount(2, $products);
        $this->assertEquals($this->productFirst->getId(), $products[0]->getId());
        $this->assertEquals($this->productSecond->getId(), $products[1]->getId());
    }

    /**
     * @depends testReindexRowAfterEdit
     */
    public function testReindexRowAfterMassAction()
    {
        $productIds = [
            $this->productFirst->getId(),
            $this->productSecond->getId(),
        ];
        $attrData = [
            'name' => 'Simple Product Common',
        ];

        /** @var \Magento\Catalog\Model\Product\Action $action */
        $action = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Catalog\Model\Product\Action'
        );
        $action->updateAttributes($productIds, $attrData, 1);

        $products = $this->search('Simple Product First');
        $this->assertCount(0, $products);

        $products = $this->search('Simple Product Second');
        $this->assertCount(0, $products);

        $products = $this->search('Simple Product Third');
        $this->assertCount(0, $products);

        $products = $this->search('Simple Product Common');
        $this->assertCount(2, $products);
        $this->assertEquals($this->productFirst->getId(), $products[0]->getId());
        $this->assertEquals($this->productSecond->getId(), $products[1]->getId());

        $products = $this->search('Simple Product');
        $this->assertCount(2, $products);
        $this->assertEquals($this->productFirst->getId(), $products[0]->getId());
        $this->assertEquals($this->productSecond->getId(), $products[1]->getId());
    }

    /**
     * @depends testReindexRowAfterMassAction
     * @magentoAppArea adminhtml
     */
    public function testReindexRowAfterDelete()
    {
        $this->productSecond->delete();

        $products = $this->search('Simple Product Common');
        $this->assertCount(1, $products);
        $this->assertEquals($this->productFirst->getId(), $products[0]->getId());
    }

    /**
     * Search the text and return result collection
     *
     * @param string $text
     * @return \Magento\Catalog\Model\Product[]
     */
    protected function search($text)
    {
        $this->resourceFulltext->resetSearchResults();
        $query = $this->queryFactory->get();
        $query->setQueryText($text)->prepare();
        $this->resourceFulltext->prepareResult($this->fulltext, $text, $query);
        $query->getResultCollection();
        $products = [];
        foreach ($query->getResultCollection() as $product) {
            $products[] = $product;
        }
        return $products;
    }

    /**
     * Return product by SKU
     *
     * @param string $sku
     * @return \Magento\Catalog\Model\Product
     */
    protected function getProductBySku($sku)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Catalog\Model\Product'
        );
        return $product->loadByAttribute('sku', $sku);
    }
}
