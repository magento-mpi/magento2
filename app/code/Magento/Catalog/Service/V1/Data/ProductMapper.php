<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data;

use \Magento\Framework\Api\ExtensibleDataObjectConverter;

class ProductMapper
{
    /** @var  \Magento\Catalog\Model\ProductFactory */
    protected $productFactory;

    /** @var  \Magento\Catalog\Model\Product\Type */
    protected $productTypes;

    /**
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Product\Type $productTypes
     */
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Product\Type $productTypes
    ) {
        $this->productFactory = $productFactory;
        $this->productTypes = $productTypes;
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
        $productModel->addData(ExtensibleDataObjectConverter::toFlatArray($product, $customAttributesToSkip));
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
