<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Product\Option;

use Magento\Bundle\Service\V1\Data\Option\MetadataConverter;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Exception\NoSuchEntityException;

class ReadService implements ReadServiceInterface
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var MetadataConverter
     */
    private $metadataConverter;

    public function __construct(MetadataConverter $metadataConverter, ProductRepository $productRepository)
    {
        $this->metadataConverter = $metadataConverter;
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function get($productSku, $optionId)
    {
        $optionCollection = $this->getOptionCollection($productSku);

        /** @var \Magento\Bundle\Service\V1\Data\Option\Metadata $optionMetadata */
        $optionMetadata = null;
        /** @var \Magento\Bundle\Model\Option $option */
        foreach ($optionCollection as $option) {
            if ($option->getId() == $optionId) {
                $optionMetadata = $this->metadataConverter->createDataFromModel($option);
            }
        }

        if ($optionMetadata === null) {
            throw new NoSuchEntityException('Requested option doesn\'t exist');
        }

        return $optionMetadata;
    }

    /**
     * {@inheritdoc}
     */
    public function getAll($productSku)
    {
        $optionCollection = $this->getOptionCollection($productSku);

        /** @var \Magento\Bundle\Service\V1\Data\Option\Metadata[] $optionMetadataList */
        $optionMetadataList = [];
        /** @var \Magento\Bundle\Model\Option $option */
        foreach ($optionCollection as $option) {
            $optionMetadataList[] = $this->metadataConverter->createDataFromModel($option);
        }
        return $optionMetadataList;
    }

    private function getOptionCollection($productSku)
    {
        $product = $this->productRepository->get($productSku);

        /** @var \Magento\Bundle\Model\Product\Type $productTypeInstance */
        $productTypeInstance = $product->getTypeInstance();

        return $productTypeInstance->getOptionsCollection($product);
    }
}
