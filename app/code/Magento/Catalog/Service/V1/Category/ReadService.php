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
use Magento\Catalog\Service\V1\Data\Eav\Category\Info\ConverterFactory;
use Magento\Catalog\Service\V1\Data\Eav\Category\Info\MetadataBuilder;
use Magento\Catalog\Service\V1\Data\Eav\Category\ProductBuilder;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;

class ReadService implements ReadServiceInterface
{
    /**
     * @var CategoryBuilder
     */
    private $builder;

    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var ConverterFactory
     */
    private $converterFactory;

    /**
     * @var ProductBuilder
     */
    private $productBuilder;

    /**
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Service\V1\Data\Eav\Category\Info\MetadataBuilder $builder
     * @param \Magento\Catalog\Service\V1\Data\Eav\Category\Info\ConverterFactory $converterFactory
     * @param ProductBuilder $productBuilder
     */
    public function __construct(
        CategoryFactory $categoryFactory,
        MetadataBuilder $builder,
        ConverterFactory $converterFactory,
        ProductBuilder $productBuilder
    ) {
        $this->builder = $builder;
        $this->categoryFactory = $categoryFactory;
        $this->converterFactory = $converterFactory;
        $this->productBuilder = $productBuilder;
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

        $metadata = $this->converterFactory->create(['builder' => $this->builder])
            ->createDataFromModel($category);

        return $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function assignedProducts($categoryId)
    {
        /** @var CategoryModel $category */
        $category = $this->categoryFactory->create();
        $category->load($categoryId);

        if (!$category->getId()) {
            throw NoSuchEntityException::singleField(Category::ID, $categoryId);
        }
        $productsPosition = $category->getProductsPosition();
        /** @var \Magento\Framework\Data\Collection\Db $products */
        $products = $category->getProductCollection();

        /** @var  $dtoProductList */
        $dtoProductList = [];
        /** @var \Magento\Catalog\Model\Product $product */
        foreach ($products->getItems() as $productId => $product) {
            $dtoProductList[] = $this->productBuilder->populateWithArray($product->getData())
                ->setPosition($productsPosition[$productId])
                ->create();
        }

        return $dtoProductList;
    }
}
