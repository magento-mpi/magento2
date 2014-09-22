<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Service\V1\Product\Option;

use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\System\Config\Source\Inputtype as InputType;
use Magento\Catalog\Service\V1\Data\Product;
use Magento\ConfigurableProduct\Model\Resource\Product\Type\Configurable\Attribute\Collection;
use Magento\ConfigurableProduct\Service\V1\Data\Option;
use Magento\ConfigurableProduct\Service\V1\Data\OptionConverter;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Webapi\Exception;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;

class ReadService implements ReadServiceInterface
{
    /**
     * @var OptionConverter
     */
    private $optionConverter;
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var ConfigurableType
     */
    private $configurableType;
    /**
     * @var InputType
     */
    private $inputType;

    /**
     * @param ProductRepository $productRepository
     * @param OptionConverter $optionConverter
     * @param ConfigurableType $configurableType
     * @param InputType $inputType
     */
    public function __construct(
        ProductRepository $productRepository,
        OptionConverter $optionConverter,
        ConfigurableType $configurableType,
        InputType $inputType
    ) {
        $this->productRepository = $productRepository;
        $this->optionConverter = $optionConverter;
        $this->configurableType = $configurableType;
        $this->inputType = $inputType;
    }

    /**
     * {@inheritdoc}
     */
    public function get($productSku, $optionId)
    {
        $product = $this->getProduct($productSku);
        $collection = $this->getConfigurableAttributesCollection($product);
        $collection->addFieldToFilter($collection->getResource()->getIdFieldName(), $optionId);
        /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute $configurableAttribute */
        $configurableAttribute = $collection->getFirstItem();
        if (!$configurableAttribute->getId()) {
            throw new NoSuchEntityException(sprintf('Requested option doesn\'t exist: %s', $optionId));
        }
        return $this->optionConverter->convertFromModel($configurableAttribute);
    }

    /**
     * {@inheritdoc}
     */
    public function getList($productSku)
    {
        $options = [];
        $product = $this->getProduct($productSku);
        foreach ($this->getConfigurableAttributesCollection($product) as $option) {
            $options[] = $this->optionConverter->convertFromModel($option);
        }
        return $options;
    }

    /**
     * @inheritdoc
     */
    public function getTypes()
    {
        return array_map(
            function ($inputType) {
                return $inputType['value'];
            },
            $this->inputType->toOptionArray()
        );
    }

    /**
     * Retrieve product instance by sku
     *
     * @param string $productSku
     * @return \Magento\Catalog\Model\Product
     * @throws \Magento\Webapi\Exception
     */
    private function getProduct($productSku)
    {
        $product = $this->productRepository->get($productSku);
        if (ConfigurableType::TYPE_CODE !== $product->getTypeId()) {
            throw new Exception(
                sprintf('Only implemented for configurable product: %s', $productSku),
                Exception::HTTP_FORBIDDEN
            );
        }
        return $product;
    }

    /**
     * Retrieve configurable attribute collection through product object
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return Collection
     */
    private function getConfigurableAttributesCollection(\Magento\Catalog\Model\Product $product)
    {
        return $this->configurableType->getConfigurableAttributeCollection($product);
    }
}
