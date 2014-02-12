<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Catalog\Model\Indexer\Category;

/**
 * @magentoDataFixture Magento/Catalog/_files/indexer_catalog_category.php
 */
class ProductTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Indexer\Model\IndexerInterface
     */
    protected $indexer;

    public static function setUpBeforeClass()
    {
        $indexer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Indexer\Model\Indexer');
        $indexer->load('catalog_category_product');
        $indexer->setScheduled(true);
    }

    public static function tearDownAfterClass()
    {
        $indexer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Indexer\Model\Indexer');
        $indexer->load('catalog_category_product');
        $indexer->setScheduled(false);
    }

    protected function setUp()
    {
        /** @var \Magento\Indexer\Model\IndexerInterface indexer */
        $this->indexer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Indexer\Model\Indexer');
        $this->indexer->load('catalog_category_product');
    }

    public function testReindexAll()
    {
        $categories = $this->getCategories(3);
        $products = $this->getProducts(2);

        /** @var \Magento\Catalog\Model\Resource\Product $productResource */
        $productResource = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Resource\Product');

        $expectedResult = array();
        /** @var \Magento\Catalog\Model\Category $category */
        $category = end($categories);
        foreach ($products as $product) {
            /** @var \Magento\Catalog\Model\Product $product */
            $product->setCategoryIds(array($category->getId()));
            $product->save();
            $expectedResult[$category->getId()][$product->getId()] = true;
            $expectedResult[$category->getParentId()][$product->getId()] = true;
        }
        ksort($expectedResult);

        /** @var \Magento\Catalog\Model\Category $category */
        $category = reset($categories);
        $category->setIsAnchor(true);
        $category->save();

        $actualResult = array();
        foreach ($categories as $category) {
            /** @var \Magento\Catalog\Model\Category $category */
            foreach ($products as $product) {
                /** @var \Magento\Catalog\Model\Product $product */
                $r = $productResource->canBeShowInCategory($product, $category->getId());
                if ($r !== false) {
                    $actualResult[$category->getId()][$product->getId()] = true;
                }
            }
        }
        ksort($actualResult);
        $this->assertNotEquals($actualResult, $expectedResult);

        $this->indexer->reindexAll();

        $actualResult = array();
        foreach ($categories as $category) {
            /** @var \Magento\Catalog\Model\Category $category */
            foreach ($products as $product) {
                /** @var \Magento\Catalog\Model\Product $product */
                $r = $productResource->canBeShowInCategory($product, $category->getId());
                if ($r !== false) {
                    $actualResult[$category->getId()][$product->getId()] = true;
                }
            }
        }
        ksort($actualResult);
        $this->assertEquals($actualResult, $expectedResult);
    }

    /**
     * @magentoAppArea adminhtml
     */
    public function testReindexList()
    {
        /** @var \Magento\Catalog\Model\Resource\Product $productResource */
        $productResource = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Resource\Product');

        $categories = $this->getCategories(4);
        $products = $this->getProducts(3);

        /** @var \Magento\Catalog\Model\Product $productLast */
        $productLast = end($products);

        /** @var \Magento\Catalog\Model\Category $category */
        $category = end($categories);
        /** @var \Magento\Catalog\Model\Category $categoryParent */
        $categoryParent = $categories[1];
        $categoryParent->setIsAnchor(true);
        $categoryParent->save();

        $productLast->setCategoryIds(array($category->getId()));
        $productLast->save();

        $expectedResult = array();
        foreach ($categories as $category) {
            /** @var \Magento\Catalog\Model\Category $category */
            foreach ($products as $product) {
                /** @var \Magento\Catalog\Model\Product $product */
                $r = $productResource->canBeShowInCategory($product, $category->getId());
                if ($r !== false) {
                    $expectedResult[$category->getId()][$product->getId()] = true;
                }
            }
        }
        ksort($expectedResult);

        $categoryNewParent = reset($categories);
        $categoryOldParent = next($categories);

        $category->getResource()->changeParent($category, $categoryNewParent, null);

        $expectedResult[$categoryNewParent->getId()][$productLast->getId()] = true;
        unset($expectedResult[$categoryOldParent->getId()]);

        $this->indexer->reindexList(
            array($category->getId(), $categoryNewParent->getId(), $categoryOldParent->getId())
        );

        $actualResult = array();
        foreach ($categories as $category) {
            /** @var \Magento\Catalog\Model\Category $category */
            foreach ($products as $product) {
                /** @var \Magento\Catalog\Model\Product $product */
                $r = $productResource->canBeShowInCategory($product, $category->getId());
                if ($r !== false) {
                    $actualResult[$category->getId()][$product->getId()] = true;
                }
            }
        }
        ksort($actualResult);
        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @magentoAppArea adminhtml
     */
    public function testReindexRow()
    {
        /** @var \Magento\Catalog\Model\Resource\Product $productResource */
        $productResource = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Resource\Product');

        $categories = $this->getCategories(3);
        $products = $this->getProducts(2);

        /** @var \Magento\Catalog\Model\Category $category */
        $category = end($categories);
        $categoryId = $category->getId();
        $category->delete();

        $this->indexer->reindexRow($categoryId);

        $actualResult = array();
        foreach ($categories as $category) {
            /** @var \Magento\Catalog\Model\Category $category */
            foreach ($products as $product) {
                /** @var \Magento\Catalog\Model\Product $product */
                $r = $productResource->canBeShowInCategory($product, $category->getId());
                if ($r !== false) {
                    $actualResult[$category->getId()][$product->getId()] = true;
                }
            }
        }
        $this->assertEquals(array(), $actualResult);
    }

    /**
     * @param int $count
     * @return \Magento\Catalog\Model\Category[]
     */
    protected function getCategories($count)
    {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Category');

        $result = $category->getCollection()->getItems();
        $result = array_slice($result, 2);

        return array_slice($result, 0, $count);
    }

    /**
     * @param int $count
     * @return \Magento\Catalog\Model\Product[]
     */
    protected function getProducts($count)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Product');

        $result = $product->getCollection()->getItems();

        return array_slice($result, 0, $count);
    }
}
