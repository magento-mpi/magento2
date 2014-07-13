<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Service\V1\Product\Link;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Service\V1\Data\Converter;

class ReadService implements ReadServiceInterface
{
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    private $productRepository;

    /**
     * @var Converter
     */
    private $productConverter;

    /**
     * @param ProductRepository $productRepository
     * @param Converter $productConverter
     */
    public function __construct(
        ProductRepository $productRepository,
        Converter $productConverter
    ) {
        $this->productRepository = $productRepository;
        $this->productConverter = $productConverter;
    }

    /**
     * @inheritdoc
     */
    public function getChildren($productId)
    {
        $product = $this->productRepository->get($productId);
        if ($product->getTypeId() != \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            return [];
        }

        $childrenList = [];

        /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable $productTypeInstance */
        $productTypeInstance = $product->getTypeInstance();
        $productTypeInstance->setStoreFilter(
            $product->getStoreId(),
            $product
        );

        foreach ($productTypeInstance->getUsedProducts($product) as $child) {
            $childrenList[] = $this->productConverter->createProductDataFromModel($child);
        }

        return $childrenList;
    }
}
