<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Category;

use Magento\Catalog\Model\Category;
use Magento\Framework\Exception\NoSuchEntityException;

class CategoryLoader
{
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    private $categoryFactory;

    /**
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     */
    public function __construct(\Magento\Catalog\Model\CategoryFactory $categoryFactory)
    {
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * @param int $categoryId
     * @return Category
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function load($categoryId)
    {
        /** @var Category $category */
        $category = $this->categoryFactory->create();
        $category->load($categoryId);

        if (!$category->getId()) {
            throw new NoSuchEntityException('There is no category with provided ID');
        }
        return $category;
    }
}
