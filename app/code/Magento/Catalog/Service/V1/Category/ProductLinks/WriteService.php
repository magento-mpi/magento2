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
use Magento\Catalog\Service\V1\Category\CategoryLoaderFactory;
use Magento\Catalog\Service\V1\Data\Category;
use Magento\Catalog\Service\V1\Data\Eav\Category\ProductLink;
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
        /** @var CategoryModel $category */
        $category = $this->categoryLoaderFactory->create()->load($categoryId);

        $productId = $this->productFactory->create()->getIdBySku($productLink->getSku());
        $productPositions = $category->getProductsPosition();

        if (array_key_exists($productId, $productPositions)) {
            throw new StateException('Product already exists in this category');
        }

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
}
