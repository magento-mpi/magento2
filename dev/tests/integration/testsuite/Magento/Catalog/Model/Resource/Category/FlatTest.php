<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Resource\Product\Category;

class FlatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Resource\Category\Flat
     */
    protected $model;

    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->model = $this->objectManager->create('Magento\Catalog\Model\Resource\Category\Flat');
    }

    /**
     * @magentoConfigFixture current_store catalog/frontend/flat_catalog_category 1
     */
    public function testGetParentDesignCategory()
    {
        $category = $this->objectManager->create('Magento\Catalog\Model\Category');
        $category->setId(3)
            ->setName('Category 1')
            ->setParentId(2)
            ->setPath('1/2/3')
            ->setLevel(2)
            ->setAvailableSortBy('name')
            ->setDefaultSortBy('name')
            ->setIsActive(true)
            ->setPosition(1)
            ->save();
        $designCategory = $this->model->getParentDesignCategory($category);
        $this->assertInstanceOf('\Magento\Catalog\Model\Category', $designCategory, 'Invalid type for category');
        $this->assertContains($designCategory->getId(), array(1, 2, 3), 'Incorrect data for parent design category');
    }
}
