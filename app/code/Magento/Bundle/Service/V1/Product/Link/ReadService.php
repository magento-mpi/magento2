<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Product\Link;

use Magento\Bundle\Model\Option;
use Magento\Bundle\Service\V1\Data\Product\Link\MetadataConverter;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\Resource\Product\Collection;

class ReadService implements ReadServiceInterface
{
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    private $productRepository;

    /**
     * @var \Magento\Bundle\Service\V1\Data\Product\Link\MetadataConverter
     */
    private $metadataConverter;

    /**
     * @param ProductRepository $productRepository
     * @param MetadataConverter $metadataConverter
     */
    public function __construct(
        ProductRepository $productRepository,
        MetadataConverter $metadataConverter
    ) {

        $this->productRepository = $productRepository;
        $this->metadataConverter = $metadataConverter;
    }

    /**
     * @inheritdoc
     */
    public function getChildren($productId)
    {
        $product = $this->productRepository->get($productId);

        $childrenList = [];
        foreach ($this->getOptions($product) as $option) {
            foreach ($option->getSelections() as $selection) {
                $childrenList[] = $this->metadataConverter->createDataFromModel($selection);
            }
        }

        return $childrenList;
    }

    /**
     * @param Product $product
     * @return Option[]
     */
    protected function getOptions(Product $product)
    {
        /** @var \Magento\Bundle\Model\Product\Type\Interceptor $productTypeInstance */
        $productTypeInstance = $product->getTypeInstance();
        $productTypeInstance->setStoreFilter(
            $product->getStoreId(),
            $product
        );

        $optionCollection = $productTypeInstance->getOptionsCollection($product);

        $selectionCollection = $productTypeInstance->getSelectionsCollection(
            $productTypeInstance->getOptionsIds($product),
            $product
        );

        $options = $optionCollection->appendSelections($selectionCollection);
        return $options;
    }
}
