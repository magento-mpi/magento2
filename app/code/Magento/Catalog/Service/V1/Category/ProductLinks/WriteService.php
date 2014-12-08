<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Category\ProductLinks;

use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Service\V1\Category\CategoryLoaderFactory;
use Magento\Catalog\Service\V1\Data\Category;
use Magento\Catalog\Service\V1\Data\Category\ProductLink;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\StateException;

class WriteService implements WriteServiceInterface
{
    /**
     * @var CategoryLoaderFactory
     */
    private $categoryLoaderFactory;

    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @param CategoryLoaderFactory $categoryLoaderFactory
     * @param ProductFactory $productFactory
     */
    public function __construct(
        CategoryLoaderFactory $categoryLoaderFactory,
        ProductFactory $productFactory
    ) {
        $this->categoryLoaderFactory = $categoryLoaderFactory;
        $this->productFactory = $productFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function assignProduct($categoryId, ProductLink $productLink)
    {
        return $this->setProductToCategory($categoryId, $productLink, true, 'Product already exists in this category');
    }

    /**
     * {@inheritdoc}
     */
    public function updateProduct($categoryId, ProductLink $productLink)
    {
        return $this->setProductToCategory($categoryId, $productLink, false, 'Product not found in this category');
    }

    /**
     * @param int $categoryId
     * @param ProductLink $productLink
     * @param bool $isInPositions
     * @param string $stateExceptionMessage
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function setProductToCategory($categoryId, ProductLink $productLink, $isInPositions, $stateExceptionMessage)
    {
        /** @var CategoryModel $category */
        $category = $this->categoryLoaderFactory->create()->load($categoryId);

        $productId = $this->productFactory->create()->getIdBySku($productLink->getSku());
        $productPositions = $category->getProductsPosition();

        if ($isInPositions === array_key_exists($productId, $productPositions)) {
            throw new StateException($stateExceptionMessage);
        }

        $newProductPositions = [$productId => $productLink->getPosition()] + $productPositions;

        $category->setPostedProducts($newProductPositions);
        try {
            $category->save();
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                'Could not save product "%product_id" with position %position to category %category_id',
                [
                    'product_id' => $productId,
                    'position' => $productLink->getPosition(),
                    'category_id' => $categoryId
                ],
                $e
            );
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function removeProduct($categoryId, $productSku)
    {
        /** @var CategoryModel $category */
        $category = $this->categoryLoaderFactory->create()->load($categoryId);
        $productId = $this->productFactory->create()->getIdBySku($productSku);

        /**
         * old category-product relationships
         */
        $productPositions = $category->getProductsPosition();
        if (!array_key_exists($productId, $productPositions)) {
            throw new StateException('Category does not contain specified product');
        }
        unset($productPositions[$productId]);
        $category->setPostedProducts($productPositions);

        try {
            $category->save();
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                'Could not remove product "%product_sku" from category with ID "%category_id"',
                [
                    'product_sku' => $productSku,
                    'category_id' => $categoryId,
                ],
                $e
            );
        }
        return true;
    }
}
