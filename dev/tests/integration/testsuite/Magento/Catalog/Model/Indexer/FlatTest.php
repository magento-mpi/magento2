<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Catalog\Model\Indexer;

class FlatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var int
     */
    protected static $categoryOne;

    /**
     * @var int
     */
    protected static $categoryTwo;

    /**
     * @var int
     */
    protected static $totalBefore = 0;

    public function testEntityItemsBefore()
    {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Category');

        $result = $category->getCollection()->getAllIds();
        $this->assertNotEmpty($result);
        $this->assertTrue(is_array($result));

        self::$totalBefore = count($result);
    }

    /**
     * Reindex All
     *
     * @magentoConfigFixture current_store catalog/frontend/flat_catalog_category true
     * @magentoAppArea frontend
     */
    public function testReindexAll()
    {
        /** @var  $indexer \Magento\Indexer\Model\IndexerInterface */
        $indexer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Indexer\Model\Indexer');
        $indexer->load('catalog_category_flat');
        $indexer->reindexAll();

        $this->assertTrue($indexer->isValid());
    }

    /**
     * @magentoConfigFixture current_store catalog/frontend/flat_catalog_category true
     * @magentoAppArea frontend
     */
    public function testFlatItemsBefore()
    {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Category')
            ->load(2);

        $this->assertInstanceOf('Magento\Catalog\Model\Resource\Category\Flat', $category->getResource());

        $result = $category->getAllChildren(true);
        $this->assertNotEmpty($result);
        $this->assertCount(1, $result);
    }

    /**
     * Reindex Row
     *
     * @magentoConfigFixture current_store catalog/frontend/flat_catalog_category true
     */
    public function testCreateCategory()
    {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Category');

        $category->setName('Category One')
            ->setPath('1/2')
            ->setAvailableSortBy('name')
            ->setDefaultSortBy('name')
            ->setIsActive(true)
            ->save();

        self::$categoryOne = $category->getId();

        /** @var \Magento\Catalog\Model\Category $category */
        $category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Category');

        $category->setName('Category Two')
            ->setPath('1/2/' . self::$categoryOne)
            ->setAvailableSortBy('name')
            ->setDefaultSortBy('name')
            ->setIsActive(true)
            ->save();

        self::$categoryTwo = $category->getId();

        $result = $category->getCollection()->getItems();
        $this->assertTrue(is_array($result));

        $this->assertEquals(2, $result[self::$categoryOne]->getParentId());
        $this->assertEquals(self::$categoryOne, $result[self::$categoryTwo]->getParentId());
    }

    /**
     * @magentoConfigFixture current_store catalog/frontend/flat_catalog_category true
     * @magentoAppArea frontend
     */
    public function testFlatAfterCreate()
    {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Category')
            ->load(2);

        $this->assertInstanceOf('Magento\Catalog\Model\Resource\Category\Flat', $category->getResource());

        $result = $category->getAllChildren(true);
        $this->assertNotEmpty($result);
        $this->assertCount(3, $result);
        $this->assertContains(self::$categoryOne, $result);

        /** @var \Magento\Catalog\Model\Category $category */
        $category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Category')
            ->load(self::$categoryOne);

        $this->assertInstanceOf('Magento\Catalog\Model\Resource\Category\Flat', $category->getResource());

        $result = $category->getAllChildren(true);
        $this->assertNotEmpty($result);
        $this->assertCount(2, $result);
        $this->assertContains(self::$categoryTwo, $result);
    }

    /**
     * Reindex List
     *
     * @magentoConfigFixture current_store catalog/frontend/flat_catalog_category true
     */
    public function testMoveCategory()
    {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Category')
            ->load(self::$categoryTwo);

        $this->assertEquals($category->getData('parent_id'), self::$categoryOne);

        $category->move(2, self::$categoryOne);

        $this->assertEquals($category->getData('parent_id'), 2);
    }

    /**
     * @magentoConfigFixture current_store catalog/frontend/flat_catalog_category true
     * @magentoAppArea frontend
     */
    public function testFlatAfterMove()
    {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Category')
            ->load(2);

        $this->assertInstanceOf('Magento\Catalog\Model\Resource\Category\Flat', $category->getResource());

        $result = $category->getAllChildren(true);
        $this->assertNotEmpty($result);
        $this->assertCount(3, $result);
    }

    /**
     * @magentoConfigFixture current_store catalog/frontend/flat_catalog_category true
     * @magentoAppArea adminhtml
     */
    public function testDeleteCategory()
    {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Category');

        $category->load(self::$categoryTwo);
        $category->delete();

        $category->load(self::$categoryOne);
        $category->delete();

        $result = $category->getCollection()->getAllIds();
        $this->assertNotEmpty($result);
        $this->assertTrue(is_array($result));
        $this->assertCount(self::$totalBefore, $result);
    }

    /**
     * @magentoConfigFixture current_store catalog/frontend/flat_catalog_category true
     * @magentoAppArea frontend
     */
    public function testFlatAfterDeleted()
    {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Category')
            ->load(2);

        $this->assertInstanceOf('Magento\Catalog\Model\Resource\Category\Flat', $category->getResource());

        $result = $category->getAllChildren(true);
        $this->assertNotEmpty($result);
        $this->assertCount(1, $result);
    }
}
