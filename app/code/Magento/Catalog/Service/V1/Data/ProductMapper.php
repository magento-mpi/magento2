<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data;

use \Magento\Framework\Service\EavDataObjectConverter;

class ProductMapper
{
    /** @var  \Magento\Catalog\Model\ProductFactory */
    protected $productFactory;

    /** @var  \Magento\Catalog\Model\Product\Type */
    protected $productTypes;

    /**
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     */
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Product\Type $productTypes
    ) {
        $this->productFactory = $productFactory;
        $this->productTypes = $productTypes;
    }

    /**
     * @param Product $product
     * @param \Magento\Catalog\Model\Product $productModel
     * @return \Magento\Catalog\Model\Product
     */
    public function toModel(
        Product $product,
        \Magento\Catalog\Model\Product $productModel = null
    ) {
        /** @var \Magento\Catalog\Model\Product $productModel */
        $productModel = $productModel ?: $this->productFactory->create();
        $productModel->addData(EavDataObjectConverter::toFlatArray($product));
        if (!is_numeric($productModel->getAttributeSetId())) {
            $productModel->setAttributeSetId($productModel->getDefaultAttributeSetId());
        }
        if (!$productModel->hasTypeId()) {
            $productModel->setTypeId(\Magento\Catalog\Model\Product\Type::DEFAULT_TYPE);
        } else if (!isset($this->productTypes->getTypes()[$productModel->getTypeId()])) {
            throw new \RuntimeException('Illegal product type');
        }
        return $productModel;
    }
}