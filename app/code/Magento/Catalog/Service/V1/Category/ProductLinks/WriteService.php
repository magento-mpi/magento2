<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Category\ProductLinks;

use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Service\V1\Data\Category;
use Magento\Catalog\Service\V1\Data\Eav\Category\ProductLink;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class WriteService implements WriteServiceInterface
{
    /**
     * @var \Magento\Catalog\Service\V1\Data\CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    private $productFactory;

    public function __construct(
        CategoryFactory $categoryFactory,
        ProductFactory $productFactory
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->productFactory = $productFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function assignProduct($categoryId, ProductLink $productLink)
    {
        $category = $this->getCategory($categoryId);
        $productId = $this->productFactory->create()->getIdBySku($productLink->getSku());
        $productPositions = $category->getProductsPosition();
        $newProductPositions = [$productId => $productLink->getPosition()] + $productPositions;
        $category->setPostedProducts($newProductPositions);
        try {
            $category->save();
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                'Could not save product "%1" with position %2 to category %3',
                [
                    $productId,
                    $productLink->getPosition(),
                    $categoryId,
                ],
                $e
            );
        }
        return true;
    }

    /**
     * @param int $id
     * @return CategoryModel
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getCategory($id)
    {
        /** @var CategoryModel $category */
        $category = $this->categoryFactory->create();
        $category->load($id);

        if (!$category->getId()) {
            throw NoSuchEntityException::singleField(Category::ID, $id);
        }
        return $category;
    }
}
