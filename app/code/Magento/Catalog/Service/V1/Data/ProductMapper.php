<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data;

class ProductMapper
{
    /** @var  \Magento\Catalog\Model\ProductFactory */
    protected $productFactory;

    /** @var  \Magento\Catalog\Model\Product\Type */
    protected $productTypes;

    /**
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;

    /**
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Product\Type $productTypes
     * @param \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Product\Type $productTypes,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->productFactory = $productFactory;
        $this->productTypes = $productTypes;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * @param  Product $product
     * @param  \Magento\Catalog\Model\Product $productModel
     * @param  string[] $customAttributesToSkip
     * @return \Magento\Catalog\Model\Product
     * @throws \RuntimeException
     */
    public function toModel(
        Product $product,
        \Magento\Catalog\Model\Product $productModel = null,
        $customAttributesToSkip = array()
    ) {
        /** @var \Magento\Catalog\Model\Product $productModel */
        $productModel = $productModel ? : $this->productFactory->create();
        $productModel->addData(
            $this->extensibleDataObjectConverter->toFlatArray(
                $product,
                '\Magento\Catalog\Service\V1\Data\Product',
                $customAttributesToSkip
            )
        );
        if (!is_numeric($productModel->getAttributeSetId())) {
            $productModel->setAttributeSetId($productModel->getDefaultAttributeSetId());
        }
        if (!$productModel->hasTypeId()) {
            $productModel->setTypeId(\Magento\Catalog\Model\Product\Type::DEFAULT_TYPE);
        } elseif (!isset($this->productTypes->getTypes()[$productModel->getTypeId()])) {
            throw new \RuntimeException('Illegal product type');
        }
        return $productModel;
    }
}
