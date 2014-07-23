<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Product\Option;

use Magento\Bundle\Service\V1\Data\Option\MetadataConverter;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Webapi\Exception;

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

    public function __construct(
        MetadataConverter $metadataConverter,
        ProductRepository $productRepository
    ) {
        $this->metadataConverter = $metadataConverter;
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function get($productSku, $optionId)
    {
        $product = $this->getProduct($productSku);
        /** @var \Magento\Bundle\Model\Product\Type $productTypeInstance */
        $productTypeInstance = $product->getTypeInstance();
        $optionCollection = $productTypeInstance->getOptionsCollection($product);

        /** @var \Magento\Bundle\Service\V1\Data\Option\Metadata $optionMetadata */
        $optionMetadata = null;
        /** @var \Magento\Bundle\Model\Option $option */
        foreach ($optionCollection as $option) {
            if ($option->getId() == $optionId) {
                $optionMetadata = $this->metadataConverter->createDataFromModel($option, $product);
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
    public function getList($productSku)
    {
        $product = $this->getProduct($productSku);
        /** @var \Magento\Bundle\Model\Product\Type $productTypeInstance */
        $productTypeInstance = $product->getTypeInstance();
        $optionCollection = $productTypeInstance->getOptionsCollection($product);

        /** @var \Magento\Bundle\Service\V1\Data\Option\Metadata[] $optionMetadataList */
        $optionMetadataList = [];
        /** @var \Magento\Bundle\Model\Option $option */
        foreach ($optionCollection as $option) {
            $optionMetadataList[] = $this->metadataConverter->createDataFromModel($option, $product);
        }
        return $optionMetadataList;
    }

    /**
     * @param string $productSku
     * @return Product
     * @throws Exception
     */
    private function getProduct($productSku)
    {
        $product = $this->productRepository->get($productSku);

        if ($product->getTypeId() != \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
            throw new Exception('Only implemented for bundle product', Exception::HTTP_FORBIDDEN);
        }

        return $product;
    }
}
