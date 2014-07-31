<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Service\V1\Product\Option;

use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\System\Config\Source\Inputtype;
use Magento\Catalog\Service\V1\Data\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable\Interceptor;
use Magento\ConfigurableProduct\Model\Resource\Product\Type\Configurable\Attribute\Collection;
use Magento\ConfigurableProduct\Service\V1\Data\Option;
use Magento\ConfigurableProduct\Service\V1\Data\OptionConverter;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Webapi\Exception;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;

class ReadService implements ReadServiceInterface
{
    const TYPE_FIELD_NAME = 'frontend_input';
    /**
     * @var OptionConverter
     */
    private $optionConverter;

    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var Interceptor
     */
    private $configurableProductTypeInstance;
    /**
     * @var \Magento\Catalog\Model\System\Config\Source\Inputtype
     */
    private $inputType;

    /**
     * @param ProductRepository $productRepository
     * @param OptionConverter $optionConverter
     * @param Interceptor $configurableProductTypeInstance
     * @param Inputtype $inputType
     */
    public function __construct(
        ProductRepository $productRepository,
        OptionConverter $optionConverter,
        Interceptor $configurableProductTypeInstance,
        Inputtype $inputType
    ) {
        $this->productRepository = $productRepository;
        $this->optionConverter = $optionConverter;
        $this->configurableProductTypeInstance = $configurableProductTypeInstance;
        $this->inputType = $inputType;
    }

    /**
     * {@inheritdoc}
     */
    public function get($productSku, $optionId)
    {
        $product = $this->getProduct($productSku);
        $collection = $this->getConfigurableAttributesCollection($product);
        $configurableAttribute = $this->getOptionById($optionId, $collection);
        return $this->optionConverter->convertFromModel($configurableAttribute);
    }

    /**
     * {@inheritdoc}
     */
    public function getList($productSku)
    {
        $options = [];
        $product = $this->getProduct($productSku);
        foreach ($this->getConfigurableAttributesCollection($product) as $configurableAttribute) {
            $options[] = $this->optionConverter->convertFromModel($configurableAttribute);
        }
        return $options;
    }

    /**
     * @inheritdoc
     */
    public function getType($productSku, $optionId)
    {
        $product = $this->getProduct($productSku);
        $collection = $this->getConfigurableAttributesCollection($product);
        $configurableAttribute = $this->getOptionById($optionId, $collection);
        return $configurableAttribute->getProductAttribute()->getData(static::TYPE_FIELD_NAME);
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
            throw new Exception("Only implemented for configurable product: $productSku", Exception::HTTP_FORBIDDEN, Exception::HTTP_BAD_REQUEST, [$productSku]);
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
        return $this->configurableProductTypeInstance->getConfigurableAttributeCollection($product);
    }

    /**
     * @param int $optionId
     * @param Collection $collection
     * @return ConfigurableType\Attribute
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getOptionById($optionId, Collection $collection)
    {
        $collection->addFieldToFilter($collection->getResource()->getIdFieldName(), $optionId);
        /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute $configurableAttribute */
        $configurableAttribute = $collection->getFirstItem();
        if (!$configurableAttribute->getId()) {
            throw new NoSuchEntityException('Requested option doesn\'t exist: %1', [$optionId]);
        }
        return $configurableAttribute;
    }
}
