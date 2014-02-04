<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Catalog\Model\Indexer\Category;

class FlatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var int
     */
    protected $categoryOneId = 3;

    /**
     * @var int
     */
    protected $categoryTwoId = 4;

    /**
     * @var int
     */
    protected $totalCategories;

    /**
     * @magentoConfigFixture current_store catalog/frontend/flat_catalog_category true
     * @magentoAppArea frontend
     */
    public function testExecuteFull()
    {
        /** @var  $indexer \Magento\Indexer\Model\IndexerInterface */
        $indexer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Indexer\Model\Indexer');
        $indexer->load('catalog_category_flat');
        $indexer->reindexAll();

        $this->assertTrue($indexer->isValid());

        /** @var \Magento\Catalog\Model\Category $category */
        $category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Category');

        $this->assertInstanceOf('Magento\Catalog\Model\Resource\Category\Flat', $category->getResource());

        $result = $category->getResource()->getCategories(1);
        $this->assertNotEmpty($result);
    }

    /**
     * This test is required for testExecuteRow and testExecuteList
     *
     * @magentoConfigFixture current_store catalog/frontend/flat_catalog_category true
     */
    public function testAddCategories()
    {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Category');

        $allIds = $category->getCollection()->getAllIds();
        $this->totalCategories = count($allIds);

        $category->setId($this->categoryOneId)
            ->setName('Category One')
            ->setParentId(2)
            ->setLevel(2)
            ->setAvailableSortBy('name')
            ->setDefaultSortBy('name')
            ->setIsActive(true)
            ->setPosition(1)
            ->save();

        $category->setPath('1/2/' . $this->categoryOneId)
            ->save();

        /** @var \Magento\Catalog\Model\Category $category */
        $category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Category');

        $category->setId($this->categoryTwoId)
            ->setName('Category Two')
            ->setParentId($this->categoryOneId)
            ->setLevel(2)
            ->setAvailableSortBy('name')
            ->setDefaultSortBy('name')
            ->setIsActive(true)
            ->setPosition(1)
            ->save();

        $category->setPath('1/2/3/' . $this->categoryTwoId)
            ->save();

        $result = $category->getCollection()->getItems();

        $this->assertTrue(is_array($result));

        $this->assertEquals('1/2/' . $this->categoryOneId, $result[$this->categoryOneId]->getPath());
        $this->assertEquals('1/2/3/' . $this->categoryTwoId, $result[$this->categoryTwoId]->getPath());
    }

    /**
     * @magentoConfigFixture current_store catalog/frontend/flat_catalog_category true
     * @magentoAppArea frontend
     */
    public function testExecuteRow()
    {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Category');

        $this->assertInstanceOf('Magento\Catalog\Model\Resource\Category\Flat', $category->getResource());

        $result = $category->getResource()->getCategories(2);
        $this->assertCount(1, $result);

        $this->assertArrayHasKey($this->categoryOneId, $result);
    }

//    /**
//     * @magentoConfigFixture current_store catalog/frontend/flat_catalog_category true
//     */
//    public function testMoveCategory()
//    {
//        /** @var \Magento\Catalog\Model\Category $category */
//        $category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
//            ->create('Magento\Catalog\Model\Category');
//
//        $this->assertEquals($category->getParentId(), 3);
//
//        $category->move(2, 3);
//
//        $this->assertEquals($category->getParentId(), 3);
//    }

}
