<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Service\V1\Product\Options;

use Magento\Catalog\Model\ProductRepository;
use Magento\ConfigurableProduct\Service\V1\Data\ConfigurableAttribute;
use Magento\Catalog\Model\Resource\Eav\AttributeFactory as EavAttributeFactory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable\AttributeFactory as ConfigurableAttributeFactory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as TypeConfigurable;
use Magento\Eav\Model\Resource\Entity\Attribute\Option\Collection as OptionCollection;
use Magento\Eav\Model\Resource\Entity\Attribute\Option\CollectionFactory as OptionCollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Webapi\Exception;

class WriteService implements WriteServiceInterface
{
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * Eav attribute factory
     *
     * @var EavAttributeFactory
     */
    protected $eavAttributeFactory;

    /**
     * Configurable attribute factory
     *
     * @var ConfigurableAttributeFactory
     */
    protected $configurableAttributeFactory;

    /**
     * @var OptionCollectionFactory
     */
    protected $optionCollection;

    /**
     * @param ProductRepository $productRepository
     * @param EavAttributeFactory $eavAttributeFactory
     * @param ConfigurableAttributeFactory $configurableAttributeFactory
     * @param OptionCollectionFactory $optionCollection
     */
    public function __construct(
        ProductRepository $productRepository,
        EavAttributeFactory $eavAttributeFactory,
        ConfigurableAttributeFactory $configurableAttributeFactory,
        OptionCollectionFactory $optionCollection
    ) {
        $this->productRepository = $productRepository;
        $this->eavAttributeFactory = $eavAttributeFactory;
        $this->configurableAttributeFactory = $configurableAttributeFactory;
        $this->optionCollection = $optionCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function add($productSku, ConfigurableAttribute $attribute)
    {
        $product = $this->productRepository->get($productSku);
        $eavAttribute = $this->loadAttribute($attribute->getAttributeCode());

        // Check whether attribute values are equals existing options of attribute
        $this->validateAttributeValues($attribute->getValues());

        $attributeData = $attribute->__toArray();
        $attributeData[ConfigurableAttribute::ATTRIBUTE_ID] = $eavAttribute->getAttributeId();

        $configurableAttribute = $this->configurableAttributeFactory->create();
        $configurableAttribute->loadByProductAndAttribute($product, $eavAttribute);
        if ($configurableAttribute->getId()) {
            throw new CouldNotSaveException('Option already added to product');
        }

        $configurableAttribute
            ->addData($attributeData)
            ->setStoreId($product->getStoreId())
            ->setProductId($product->getId())
            ->save();

        return $configurableAttribute->getId();
    }

    /**
     * Retrieve eav attribute by attribute code
     *
     * @param string $attributeCode
     * @return $this
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function loadAttribute($attributeCode)
    {
        $eavAttribute = $this->eavAttributeFactory->create()
            ->loadByCode(\Magento\Catalog\Model\Product::ENTITY, $attributeCode);
        if (!$eavAttribute->getId()) {
            throw new NoSuchEntityException('Requested attribute doesn\'t exist');
        }
        if (!$eavAttribute->getIsConfigurable()) {
            throw new NoSuchEntityException('Attribute can not be applied for configurable product');
        }
        return $eavAttribute;
    }

    /**
     * Check whether attribute values are equals existing options of attribute
     *
     * @param array $values
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function validateAttributeValues(array $values)
    {
        /** @var OptionCollection $collection */
        $collection = $this->optionCollection->create();
        foreach ($values as $value) {
            if (!$collection->getItemById($value->getIndex())) {
                throw new NoSuchEntityException(
                    sprintf('There is no option with provided index: "%s"', $value->getIndex())
                );
            }
        }
    }
}
