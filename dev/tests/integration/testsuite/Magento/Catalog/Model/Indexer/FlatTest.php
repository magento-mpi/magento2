<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer;

/**
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class FlatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Flat
     */
    protected $model;

    /**
     * @var \Magento\Indexer\Model\Indexer
     */
    protected $indexer;

    protected static $categoryIds = array();

    protected function setUp()
    {
        $this->model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Indexer\Category\Flat');
        $this->indexer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Indexer\Model\Indexer');
    }

    /**
     * @magentoAppArea adminhtml
     */
    public function testExecuteFullWithoutChanges()
    {
        $this->indexer->load(\Magento\Catalog\Model\Indexer\Category\Flat\State::INDEXER_ID)
            ->setScheduled(true);
        $this->indexer->getState()
            ->setStatus(\Magento\Indexer\Model\Indexer\State::STATUS_VALID)
            ->save();
        $this->model->executeFull();
        $this->createCategory();
        /** @var \Magento\Catalog\Model\Resource\Category\Flat $flatResource */
        $flatResource = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Resource\Category\Flat');
        $categoryId = reset(self::$categoryIds);
        $categoryChild = $flatResource->getCategories($categoryId);
        $this->assertEquals(array(), array_keys($categoryChild));
    }

    /**
     * @magentoConfigFixture current_store catalog/frontend/flat_catalog_category true
     */
    public function testExecuteFullWithNewCategories()
    {
        $this->model->executeFull();
        $categoryId = reset(self::$categoryIds);
        /** @var \Magento\Catalog\Model\Category $category */
        $category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Category')
            ->load($categoryId);
        $this->assertInstanceOf('Magento\Catalog\Model\Resource\Category\Flat', $category->getResource());
        $result = $category->getResource()->getAllChildren($category);
        $this->assertEquals(self::$categoryIds, $result);
    }

    /**
     * @magentoAppArea adminhtml
     */
    public function testCleanupFull()
    {
        $this->clearCategories();
    }

    /**
     * @magentoConfigFixture current_store catalog/frontend/flat_catalog_category true
     * @magentoDataFixture Magento/Catalog/_files/flat_list_categories.php
     */
    public function testExecuteList()
    {
        $this->assertFalse(false);
    }

    protected function createCategory()
    {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Category');
        $category->setName(uniqid('Category First '))
            ->setParentId(2)
            ->setLevel(2)
            ->setAvailableSortBy('name')
            ->setDefaultSortBy('name')
            ->setIsActive(true)
            ->setPosition(1)
            ->save();
        $category->setPath('1/2/' . $category->getId())->save();
        self::$categoryIds[] = $category->getId();

        $categoryNew = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Category');
        $categoryNew->setName(uniqid('Category Second '))
            ->setParentId($category->getId())
            ->setLevel(3)
            ->setAvailableSortBy('name')
            ->setDefaultSortBy('name')
            ->setIsActive(true)
            ->setPosition(1)
            ->save();
        $categoryNew->setPath($category->getPath() . '/' . $categoryNew->getId())->save();
        self::$categoryIds[] = $categoryNew->getId();

        $categoryNew = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Category');
        $categoryNew->setName(uniqid('Category Third '))
            ->setParentId($category->getId())
            ->setLevel(3)
            ->setAvailableSortBy('name')
            ->setDefaultSortBy('name')
            ->setIsActive(true)
            ->setPosition(2)
            ->save();
        $categoryNew->setPath($category->getPath() . '/' . $categoryNew->getId())->save();
        self::$categoryIds[] = $categoryNew->getId();
    }

    protected function clearCategories()
    {
        self::$categoryIds = array_reverse(self::$categoryIds);
        foreach (self::$categoryIds as $categoryId) {
            /** @var \Magento\Catalog\Model\Category $category */
            $category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                ->create('Magento\Catalog\Model\Category')
                ->load($categoryId);
            $category->delete();
        }

        self::$categoryIds = array();
    }
}
