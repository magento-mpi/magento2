<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Category;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class WriteService implements WriteServiceInterface
{
    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(CategoryFactory $categoryFactory)
    {
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($categoryId)
    {
        /** @var Category $category */
        $category = $this->categoryFactory->create();
        $category->load($categoryId);

        if (!$category || !$category->getId()) {
            // TODO: need change from static text to const
            throw NoSuchEntityException::singleField('id', $categoryId);
        }

        try {
            $category->delete();
        } catch (\Exception $e) {
            throw new CouldNotSaveException('Cannot delete category with id %1', [$categoryId], $e);
        }

        return true;
    }
}
