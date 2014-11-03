<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model;

use Magento\Framework\Exception\CouldNotSaveException;

class CategoryLinkRepository implements \Magento\Catalog\Api\CategoryLinkRepositoryInterface
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @param CategoryRepository $categoryRepository
     * @param ProductRepository $productRepository
     */
    public function __construct(
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Magento\Catalog\Api\Data\CategoryProductLinkInterface $productLink)
    {
        $category = $this->categoryRepository->get($productLink->getCategoryId());
        $product = $this->productRepository->get($productLink->getSku());
        $productPositions = $category->getProductsPosition();
        $productPositions[$product->getId()] = $productLink->getPosition();
        $category->setPostedProducts($productPositions);
        try {
            $category->save();
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                'Could not save product "%product_id" with position %position to category %category_id',
                [
                    'product_id' => $product->getId(),
                    'position' => $productLink->getPosition(),
                    'category_id' => $category->getId()
                ],
                $e
            );
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Magento\Catalog\Api\Data\CategoryProductLinkInterface $productLink)
    {
        return $this->deleteByIds($productLink->getCategoryId(), $productLink->getSku());
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByIds($categoryId, $productSku)
    {
        $category = $this->categoryRepository->get($categoryId);
        $product = $this->productRepository->get($productSku);
        $productPositions = $category->getProductsPosition();

        unset($productPositions[$product->getId()]);

        $category->setPostedProducts($productPositions);
        try {
            $category->save();
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                'Could not save product "%product_id" with position %position to category %category_id',
                [
                    'product_id' => $product->getId(),
                    'category_id' => $category->getId()
                ],
                $e
            );
        }
        return true;
    }
}
