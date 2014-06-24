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
use Magento\Catalog\Service\V1\Data\Eav\Category\ProductConverterFactory;
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
     * @var ProductConverterFactory
     */
    private $productConverterFactory;

    /**
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Service\V1\Data\Eav\Category\Info\MetadataBuilder $builder
     * @param \Magento\Catalog\Service\V1\Data\Eav\Category\Info\ConverterFactory $converterFactory
     * @param ProductBuilder $productBuilder
     * @param ProductConverterFactory $productConverterFactory
     */
    public function __construct(
        CategoryFactory $categoryFactory,
        MetadataBuilder $builder,
        ConverterFactory $converterFactory,
        ProductBuilder $productBuilder,
        ProductConverterFactory $productConverterFactory
    ) {
        $this->builder = $builder;
        $this->categoryFactory = $categoryFactory;
        $this->converterFactory = $converterFactory;
        $this->productBuilder = $productBuilder;
        $this->productConverterFactory = $productConverterFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function info($categoryId)
    {
        $category = $this->getCategory($categoryId);

        $metadata = $this->converterFactory->create(['builder' => $this->builder])
            ->createDataFromModel($category);

        return $metadata;
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

        /** @var \Magento\Catalog\Service\V1\Data\Eav\Category\Product $dtoProductList */
        $dtoProductList = [];

        /** @var \Magento\Catalog\Service\V1\Data\Eav\Category\ProductConverter $productConverter */
        $productConverter = $this->productConverterFactory->create(['productBuilder' => $this->productBuilder]);

        /** @var \Magento\Catalog\Model\Product $product */
        foreach ($products->getItems() as $productId => $product) {
            $productConverter->setPosition($productsPosition[$productId]);
            $dtoProductList[] = $productConverter->createProductDataFromModel($product);
        }

        return $dtoProductList;
    }

    /**
     * @param $id
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
