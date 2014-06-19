<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Category;

use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Service\V1\Data\Category;
use Magento\Catalog\Service\V1\Data\CategoryBuilder;
use Magento\Catalog\Service\V1\Data\Eav\Category\AttributeMetadata;
use Magento\Framework\Exception\NoSuchEntityException;

class ReadService implements ReadServiceInterface
{
    /**
     * @var CategoryBuilder
     */
    private $categoryBuilder;

    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * @param CategoryFactory $categoryFactory
     * @param CategoryBuilder $categoryBuilder
     */
    public function __construct(CategoryFactory $categoryFactory, CategoryBuilder $categoryBuilder)
    {
        $this->categoryBuilder = $categoryBuilder;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function info($categoryId)
    {
        /** @var CategoryModel $category */
        $category = $this->categoryFactory->create();
        $category->load($categoryId);

        if (!$category->getId()) {
            throw NoSuchEntityException::singleField(Category::ID, $categoryId);
        }
        /** @var AttributeMetadata $categoryMetadata */
        $categoryMetadata = $this->categoryBuilder->populateWithArray($category->getData())->create();

        return $categoryMetadata;
    }
}
