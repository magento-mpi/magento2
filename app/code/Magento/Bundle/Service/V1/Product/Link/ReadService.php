<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Product\Link;

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
     * @var \Magento\Catalog\Model\Resource\Product\Collection
     */
    private $productCollection;

    /**
     * @var \Magento\Bundle\Service\V1\Data\Product\Link\MetadataConverter
     */
    private $metadataConverter;

    /**
     * @param ProductRepository $productRepository
     * @param Collection $productCollection
     * @param MetadataConverter $metadataConverter
     */
    public function __construct(
        ProductRepository $productRepository,
        Collection $productCollection,
        MetadataConverter $metadataConverter
    ) {

        $this->productRepository = $productRepository;
        $this->productCollection = $productCollection;
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
            /** @var \Magento\Bundle\Model\Option $option */
            foreach ($option->getSelections() as $selection) {
                $childrenList[] = $this->metadataConverter->createDataFromModel($selection);
            }
        }

        return $childrenList;
    }

    protected function getOptions(Product $product)
    {
        $product->getTypeInstance()->setStoreFilter(
            $product->getStoreId(),
            $product
        );

        $optionCollection = $product->getTypeInstance()->getOptionsCollection($product);

        $selectionCollection = $product->getTypeInstance()->getSelectionsCollection(
            $product->getTypeInstance()->getOptionsIds($product),
            $product
        );

        $options = $optionCollection->appendSelections($selectionCollection);
        return $options;
    }
}
