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
use Magento\Catalog\Service\V1\Data\Category;
use Magento\Catalog\Service\V1\Data\Category\ProductLink;
use Magento\Catalog\Service\V1\Data\Category\ProductLinkBuilder;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;

class ReadService implements ReadServiceInterface
{
    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var ProductLinkBuilder
     */
    private $productLinkBuilder;

    /**
     * @param CategoryFactory $categoryFactory
     * @param ProductLinkBuilder $productLinkBuilder
     */
    public function __construct(
        CategoryFactory $categoryFactory,
        ProductLinkBuilder $productLinkBuilder
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->productLinkBuilder = $productLinkBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function assignedProducts($categoryId)
    {
        $category = $this->getCategory($categoryId);

        $productsPosition = $category->getProductsPosition();
        /** @var \Magento\Framework\Data\Collection\Db $products */
        $products = $category->getProductCollection();

        /** @var \Magento\Catalog\Service\V1\Data\Eav\Category\Product[] $dtoProductList */
        $dtoProductList = [];

        /** @var \Magento\Catalog\Model\Product $product */
        foreach ($products->getItems() as $productId => $product) {
            $dtoProductList[] = $this->productLinkBuilder->populateWithArray(
                [ProductLink::SKU => $product->getSku(), ProductLink::POSITION => $productsPosition[$productId]]
            )->create();
        }

        return $dtoProductList;
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
