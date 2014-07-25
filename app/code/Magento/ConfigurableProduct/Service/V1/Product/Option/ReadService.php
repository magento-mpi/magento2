<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Service\V1\Product\Option;

use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Service\V1\Data\Product;
use Magento\Catalog\Service\V1\Product\Attribute;
use Magento\ConfigurableProduct\Service\V1\Data\ConfigurableAttribute;
use Magento\ConfigurableProduct\Service\V1\Data\ConfigurableAttributeConverter;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Webapi\Exception;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;

class ReadService implements ReadServiceInterface
{
    private $configurableAttributeConverter;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @param ProductRepository $productRepository
     * @param ConfigurableAttributeConverter $configurableAttributeConverter
     */
    public function __construct(
        ProductRepository $productRepository,
        ConfigurableAttributeConverter $configurableAttributeConverter
    ) {
        $this->productRepository = $productRepository;
        $this->configurableAttributeConverter = $configurableAttributeConverter;
    }


    /**
     * {@inheritdoc}
     */
    public function get($productSku, $optionId)
    {
        $attributeMetadata = null;
        $product = $this->getProduct($productSku);
        $option = null;

        /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute $configurableAttribute */
        $option = $this->getConfigurableAttributesCollection($product)->getItemById($optionId);
//        foreach ($this->getConfigurableAttributesCollection($product) as $configurableAttribute) {
//            if ($optionId == $configurableAttribute->getAttributeId()) {
//                $option = $configurableAttribute;
//                break;
//            }
//        }

        if (is_null($option)) {
            throw new NoSuchEntityException('Requested option doesn\'t exist');
        }

        return $this->configurableAttributeConverter->createDataFromModel($option);
    }

    /**
     * {@inheritdoc}
     */
    public function getList($productSku)
    {
        /** @var ConfigurableAttribute[] $attributes */
        $attributes = [];
        $product = $this->getProduct($productSku);
        $attributesCollection = $this->getConfigurableAttributesCollection($product);

        /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute $attribute */
        foreach ($attributesCollection as $attribute) {
            $attributes[] = $this->configurableAttributeConverter->createDataFromModel($attribute);
        }

        return $attributes;
    }


    /**
     * @param string $productSku
     * @return \Magento\Catalog\Model\Product
     * @throws \Magento\Webapi\Exception
     */
    private function getProduct($productSku)
    {
        $product = $this->productRepository->get($productSku);
        if (ConfigurableType::TYPE_CODE !== $product->getTypeId()) {
            throw new Exception('Only implemented for configurable product');
        }
        return $product;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\ConfigurableProduct\Model\Resource\Product\Type\Configurable\Attribute\Collection
     */
    private function getConfigurableAttributesCollection(\Magento\Catalog\Model\Product $product)
    {
        /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Interceptor $typeInstance */
        $typeInstance = $product->getTypeInstance();
        $collection = $typeInstance->getConfigurableAttributeCollection($product);
        return $collection;
    }
}
