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
     * Assign a product to the required category
     *
     * @param \Magento\Catalog\Api\Data\CategoryProductLinkInterface $productLink
     * @return bool will returned True if assigned
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
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
     * Remove the product assignment from the category
     *
     * @param \Magento\Catalog\Api\Data\CategoryProductLinkInterface $productLink
     * @return bool will returned True if products successfully deleted
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\StateException
     *
     * @see \Magento\Catalog\Service\V1\Category\ProductLinks\WriteServiceInterface::removeProduct - previous interface
     */
    public function delete(\Magento\Catalog\Api\Data\CategoryProductLinkInterface $productLink)
    {
        $category = $this->categoryRepository->get($productLink->getCategoryId());
        $product = $this->productRepository->get($productLink->getSku());
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
                    'position' => $productLink->getPosition(),
                    'category_id' => $category->getId()
                ],
                $e
            );
        }
        return true;
    }
}
