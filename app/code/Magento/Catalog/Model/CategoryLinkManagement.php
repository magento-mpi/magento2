<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model;

use \Magento\Catalog\Api\Data\CategoryProductLinkInterface as ProductLink;

class CategoryLinkManagement implements \Magento\Catalog\Api\CategoryLinkManagementInterface
{
    /**
     * @var \Magento\Catalog\Model\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var \Magento\Catalog\Api\Data\CategoryProductLinkInterfaceBuilder
     */
    protected $productLinkBuilder;

    /**
     * {@inheritdoc}
     */
    public function getAssignedProducts($categoryId)
    {
        $category = $this->categoryRepository->get($categoryId);
        $productsPosition = $category->getProductsPosition();

        /** @var \Magento\Framework\Data\Collection\Db $products */
        $products = $category->getProductCollection();

        /** @var \Magento\Catalog\Api\Data\CategoryProductLinkInterface[] $links */
        $links = [];

        /** @var \Magento\Catalog\Model\Product $product */
        foreach ($products->getItems() as $productId => $product) {
            $links[] = $this->productLinkBuilder->populateWithArray(
                [ProductLink::SKU => $product->getSku(), ProductLink::POSITION => $productsPosition[$productId]]
            )->create();
        }
        return $links;
    }
}
